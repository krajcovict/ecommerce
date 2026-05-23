<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => (new \DateTime($this->created_at))
                ->format('Y M j, H:i'),
            'total_price' => $this->total_price,
            'number_of_items' => $this->items()->count(),
            'user' => new UserResource($this->user),
            'customer' => new UserCustomerResource($this->user),
            'updated_at' => (new \DateTime($this->updated_at))
                ->format('Y M j, H:i'),
        ];
    }
}
