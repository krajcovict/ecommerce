<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Resources\OrderListResource;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {

        $search = request('search', false);
        $perPage = request('per_page', 20);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Order::query();
        $query->orderBy($sortField, $sortDirection);
        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('total_price', 'like', "%{$search}%");
        }

        return OrderListResource::collection(
            $query->paginate($perPage)
        );
    }
}
