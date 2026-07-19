<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Helpers\Cart;
use App\Mail\NewOrderEmail;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    public function checkout(Request $request) {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer->billingAddress || !$customer->shippingAddress) {
            return redirect()->route('details')->with('error', 'Please provide your address details first.');
        }

        $stripe = new \Stripe\StripeClient([
          "api_key" => getenv('STRIPE_SECRET_KEY')
        ]);

        [$products, $cartItems] = Cart::getProductsAndCartItems();

        $lineItems = [];
        $orderItems = [];
        $totalPrice = 0;

        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            if ($product->quantity !== null && $product->quantity < $quantity) {
                $message = match ($product->quantity) {
                    0 => 'The product "'. $product->title .'" is out of stock',
                    1 => 'There is only one item left for product "' . $product->title. '"',
                    default => 'There are only ' . $product->quantity . ' items left for product "'. $product->title . '"',
                };
                return redirect()->back()->with('error', $message);
            }
        }

        DB::beginTransaction();

// TODO: Repair Stripe checkout with 'noimage' item
        foreach ($products as $product) {
            $quantity = $cartItems[$product->id]['quantity'];
            $totalPrice += $product->price * $quantity;
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->title,
                        'images' => [$product->image],
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => $quantity,
            ];
            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
            ];
        }

        if ($product->quantity !== null) {
                $product->quantity -= $quantity;
                $product->save();
            }

        $checkout_session = $stripe->checkout->sessions->create
            ([
              'line_items' => $lineItems,
              'mode' => 'payment',
              'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
              'cancel_url' => route('checkout.failure', [], true),
            ]);

        try {
            // Create Order in our database
            $orderData = [
                'total_price' => $totalPrice,
                'status' => OrderStatus::Unpaid,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];
            $order = Order::create($orderData);

            // Create Order Items in our database
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
            }

            // Create Payment in our database
            $paymentData = [
                'order_id' => $order->id,
                'amount' => $totalPrice,
                'status' => PaymentStatus::Pending,
                'type' => 'cc',
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'session_id' => $checkout_session->id,
            ];
            Payment::create($paymentData);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::critical(__METHOD__ . " method failed. " . $e->getMessage());
            throw $e;
        }
        DB::commit();

        CartItem::query()->where(['user_id' => $user->id])->delete();

        return redirect()->away($checkout_session->url, 303);
    }

    public function success(Request $request)
    {
        $stripe = new \Stripe\StripeClient([
          "api_key" => getenv('STRIPE_SECRET_KEY')
        ]);

        $user = $request->user();

        try {
            $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
            if (!$session) {
                return view('checkout.failure', ['message' => 'Session ID is invalid.']);
            }

            $session_id = $session->id;

            $payment = Payment::query()
                ->with('order.user.customer')
                ->where('session_id', $session_id)
                ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
                ->first();
            if (!$payment) {
                return throw new NotFoundHttpException();
            }
            if ($payment->status === PaymentStatus::Pending->value) {
                $this->updateOrderAndPayment($payment);
            }

            $customer = $payment->order?->user?->customer;

            return view('checkout.success', [
                'firstName' => $customer?->first_name,
                'lastName' => $customer?->last_name,
            ]);
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return view('checkout.failure', ['message' => $e->getMessage()]);
        }
    }

    public function failure(Request $request)
    {
        return view('checkout.failure', ['message' => 'Payment has been cancelled.']);
    }

    public function checkoutOrder(Order $order, Request $request)
    {
        $stripe = new \Stripe\StripeClient([
          "api_key" => getenv('STRIPE_SECRET_KEY')
        ]);

        $lineItems = [];

        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->title,
                        'images' => [$item->product->image],
                    ],
                    'unit_amount' => $item->product->price * 100,
                ],
                'quantity' => $item->quantity,
            ];
        }

        $checkout_session = $stripe->checkout->sessions->create
            ([
              'line_items' => $lineItems,
              'mode' => 'payment',
              'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
              'cancel_url' => route('checkout.failure', [], true),
            ]);

        $order->payment->session_id = $checkout_session->id;
        $order->payment->save();

        return redirect()->away($checkout_session->url, 303);
    }

    public function webhook()
    {
        $stripe = new \Stripe\StripeClient([
          "api_key" => getenv('STRIPE_SECRET_KEY')
        ]);

        // STRIPE_WEBHOOK_SECRET
        // $stripe = new \Stripe\StripeClient($stripeSecretKey);
        // Replace this endpoint secret with your endpoint's unique secret
        // If you are testing with the CLI, find the secret by running 'stripe listen'
        // If you are using an endpoint defined with the API or dashboard, look in your webhook settings
        // at https://dashboard.stripe.com/webhooks
        $endpoint_secret = getenv('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $event = null;

        try {
          $event = \Stripe\Event::constructFrom(
            json_decode($payload, true)
          );
        } catch(\UnexpectedValueException $e) {
          // Invalid payload
          echo '⚠️  Webhook error while parsing basic request.';
          return response('', 401);
          exit();
        }
        if ($endpoint_secret) {
          // Only verify the event if there is an endpoint secret defined
          // Otherwise use the basic decoded event
          $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
          try {
            $event = \Stripe\Webhook::constructEvent(
              $payload, $sig_header, $endpoint_secret
            );
          } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            echo '⚠️  Webhook error while validating signature.';
            return response('', 402);
            exit();
          }
        }

        // Handle the event
        switch ($event->type) {
          case 'checkout.session.completed':
            $checkout = $event->data->object;
            $session_id = $checkout['id'];
            $payment = Payment::query()->where(['session_id' => $session_id, 'status' => PaymentStatus::Pending,])
                ->first();

            if ($payment) {
                $this->updateOrderAndPayment($payment);
            }

            break;
          default:
            // Unexpected event type
            error_log('Received unknown event type');
        }

        return response('', 200);
    }

    private function updateOrderAndPayment(Payment $payment)
    {
        DB::beginTransaction();
        try {
            $payment->status = PaymentStatus::Paid->value;
            $payment->update();

            $order = $payment->order;
            $order->status = OrderStatus::Paid->value;
            $order->update();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::critical(__METHOD__ . " method failed. " . $e->getMessage());
            throw $e;
        }
        DB::commit();

        // Send emails to the customer and admin users
        try {
            $adminUsers = User::where('is_admin', 1)->get();
            foreach ([...$adminUsers, $order->user] as $user) {
                Mail::to($user)->send(new NewOrderEmail($order, (bool)$user->is_admin));
            }
        } catch (\Throwable $th) {
            Log::critical("Email sending from CheckoutController updateOrderAndPayment() doesn't work. ". $th->getMessage());
        }
    }
}
