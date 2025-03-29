<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event_id' => $this->id,
            'seat_plan' => $this->seatPlanImage?->image_url,
            'ticket_tiers' => $this->ticketTiers->map(fn($tier) => [
                'id' => $tier->id,
                'description' => $tier->ticket_desc,
                'tier_name' => $tier->tier_name,
                'price' => $tier->price,
                'seats' => $tier->seatsByTicketTier->toArray(),
            ]),
        ];
    }
}
