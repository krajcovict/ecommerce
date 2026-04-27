<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Helpers\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Request $request) {
        $user = $request->user();

        $stripe = new \Stripe\StripeClient([
          "api_key" => getenv('STRIPE_SECRET_KEY')
        ]);

        [$products, $cartItems] = Cart::getProductsAndCartItems();

        $lineItems = [];
        $totalPrice = 0;

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
        }

        $checkout_session = $stripe->checkout->sessions->create
            ([
              'line_items' => $lineItems,
              'mode' => 'payment',
              'success_url' => route('checkout.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
              'cancel_url' => route('checkout.failure', [], true),
            ]);

        $orderData = [
            'total_price' => $totalPrice,
            'status' => OrderStatus::Unpaid,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];
        $order = Order::create($orderData);

        // echo '<pre>';
        // var_dump($checkout_session->id,);
        // echo '</pre>';

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

        // echo '<pre>';
        // var_dump($paymentData);
        // echo '</pre>';
        // exit;

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
                return view('checkout.failure');
            }

            $payment = Payment::query()->where(['session_id' => $session->id, 'status' => PaymentStatus::Pending])->first();
            if (!$payment) {
                return view('checkout.failure');
            }
            $payment->status = PaymentStatus::Paid;
            $payment->update();

            $order = $payment->order;

            $order->status = OrderStatus::Paid;
            $order->update();


            CartItem::query()->where(['user_id' => $user->id])->delete();

            //dd($order->created_by);
            //$customer = $stripe->customers->retrieve($session->customer);

            //return view('checkout.success', compact('customer'));
            // TODO: implement customer
            return view('checkout.success');
        } catch (\Exception $e) {
            return view('checkout.failure', ['message' => $e->getMessage()]);
        }
    }

    public function failure(Request $request)
    {
        return view('checkout.failure');
    }

}
