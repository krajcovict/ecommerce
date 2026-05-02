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

            $payment = Payment::query()->where(['session_id' => $session->id, 'status' => PaymentStatus::Pending])->first();
            if (!$payment) {
                return view('checkout.failure', ['message' => 'Payment failed.']);
            }
            $payment->status = PaymentStatus::Paid;
            $payment->update();

            $order = $payment->order;

            $order->status = OrderStatus::Paid;
            $order->update();


            CartItem::query()->where(['user_id' => $user->id])->delete();

            //dd($order->created_by);
            //$customer = $stripe->customers->retrieve($session->customer);
            //$name = $user->customer->id;
            //return view('checkout.success', compact('customer'));
            // TODO: implement customer
            return view('checkout.success');
            //return view('checkout.success', compact('name'));
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
        $user = $request->user();

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
}
