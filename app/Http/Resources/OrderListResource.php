<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $customer = $user?->customer;

        return [
            'id' => $this->id,
            'status' => $this->status,
            'total_price' => $this->total_price,
            //'number_of_items' => $this->items_count,

            'customer' => [
                'id' => $user?->id,
                'first_name' => $customer?->first_name,
                'last_name' => $customer?->last_name,
            ],

            'created_at' => (new \DateTime($this->created_at))
                ->format('Y M j, H:i'),
            'updated_at' => (new \DateTime($this->updated_at))
                ->format('Y M j, H:i'),
        ];
    }
}
