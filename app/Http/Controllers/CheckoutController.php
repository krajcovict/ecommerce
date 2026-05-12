<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Helpers\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    public function checkout(Request $request) {
        $user = $request->user();

        $stripe = new \Stripe\StripeClient([
          "api_key" => getenv('STRIPE_SECRET_KEY')
        ]);

        [$products, $cartItems] = Cart::getProductsAndCartItems();

        $lineItems = [];
        $orderItems = [];
        $totalPrice = 0;
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

        $checkout_session = $stripe->checkout->sessions->create
            ([
              'line_items' => $lineItems,
              'mode' => 'payment',
              'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
              'cancel_url' => route('checkout.failure', [], true),
            ]);

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

            $payment = Payment::query()->where('session_id', $session_id)
                ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
                ->first();
            if (!$payment) {
                return throw new NotFoundHttpException();
            }
            if ($payment->status === PaymentStatus::Pending) {
                $this->updateOrderAndPayment($payment);
            }

            //dd($payment->created_by);
            //$customer = $stripe->customers->retrieve($session->customer);
            //$name = $customer->user->id;
            //return view('checkout.success', compact('customer'));
            // TODO: implement customer
            return view('checkout.success');
            //return view('checkout.success', compact('name'));
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
        $payment->status = PaymentStatus::Paid;
        $payment->update();

        $order = $payment->order;
        $order->status = OrderStatus::Paid;
        $order->update();
    }
}
