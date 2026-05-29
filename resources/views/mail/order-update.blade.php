<div>
    <h2>Order status has been updated</h2>
    <p>Status of your order has been changed to {{ $order->status }}</p>
    <p><a href="{{ route('order.view', $order, true) }}">
        Link to your order #{{ $order->id }}
    </a></p>
</div>
