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
        // If this is a collection, handle it properly
        if ($this->resource instanceof \Illuminate\Database\Eloquent\Collection) {
            return $this->resource->map(function ($seat) {
                // Get the ticket tier price even if ticket is null
                $ticketTier = $seat->ticket?->ticketTier ?? $seat->getTicketTierEvenIfNoTicket();
                
                return [
                    'id' => $seat->id,
                    'ticket_id' => $seat->ticket_id ?? null,
                    'price' => $ticketTier?->price ?? null,
                    'event_id' => $seat->event_id,
                    'row' => $seat->row,
                    'number' => $seat->number,
                    'section' => $seat->section,
                    'is_occupied' => $seat->is_occupied,
                ];
            })->toArray();
        }

        // For single model, use the same approach
        $ticketTier = $this->ticket?->ticketTier ?? $this->getTicketTierEvenIfNoTicket();
        
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id ?? null,
            'price' => $ticketTier?->price ?? null,
            'event_id' => $this->event_id,
            'row' => $this->row,
            'number' => $this->number,
            'section' => $this->section,
            'is_occupied' => $this->is_occupied,
        ];
    }
}