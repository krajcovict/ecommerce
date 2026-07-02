<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Mail\OrderUpdateEmail;
use App\Models\Order;
use App\Http\Resources\OrderListResource;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {

        $search = request('search', false);
        $perPage = request('per_page', 20);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Order::query()
            ->withCount('items')
            ->with('user.customer')
            ->orderBy($sortField, $sortDirection);
        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('total_price', 'like', "%{$search}%");
        }

        return OrderListResource::collection(
            $query->paginate($perPage)
        );
    }

    public function view(Order $order)
    {
        $order->load('items.product');
        return new OrderResource($order);

    }

    public function getStatuses()
    {
        return OrderStatus::getStatuses();
    }

    public function changeStatus(Order $order, $status)
    {
        $order->status = $status;
        $order->save();

        // Send an email
        Mail::to($order->user)->send(new OrderUpdateEmail($order));

        return response('', 200);
    }
}
