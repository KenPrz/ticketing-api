<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_name' => $this->ticket_name,
            'qr_code' => $this->qr_code,
            'ticket_type' => $this->ticket_type,
            'ticket_desc' => $this->ticket_desc,
            'is_used' => $this->is_used,
            'used_on' => $this->used_on,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Event information
            'event' => $this->when($this->relationLoaded('event'), function () {
                return [
                    'id' => $this->event->id,
                    'name' => $this->event->name,
                    'date' => $this->event->date,
                    'time' => $this->event->time,
                    'venue' => $this->event->venue,
                    'city' => $this->event->city,
                    'description' => $this->event->description,
                    'thumbnail' => $this->event->thumbnail?->url,
                ];
            }),
            
            // Ticket tier information
            'ticket_tier' => $this->when($this->relationLoaded('ticketTier'), function () {
                return [
                    'id' => $this->ticketTier->id,
                    'tier_name' => $this->ticketTier->tier_name,
                    'price' => $this->ticketTier->price,
                    'ticket_type' => $this->ticketTier->ticket_type,
                ];
            }),
            
            // Owner information
            'owner' => $this->when($this->relationLoaded('owner'), function () {
                return [
                    'id' => $this->owner->id,
                    'name' => $this->owner->name,
                    'email' => $this->owner->email,
                    'mobile' => $this->owner->mobile,
                ];
            }),
            
            // Purchase information
            'purchase' => $this->when($this->relationLoaded('purchase'), function () {
                return [
                    'id' => $this->purchase->id,
                    'transaction_id' => $this->purchase->placeholder_for_transaction_handler,
                    'purchased_at' => $this->purchase->created_at,
                    'purchased_by' => [
                        'id' => $this->purchase->purchaser->id,
                        'name' => $this->purchase->purchaser->name,
                    ],
                ];
            }),
            
            // Seat information
            'seat' => $this->when($this->relationLoaded('seat'), function () {
                return $this->seat ? [
                    'id' => $this->seat->id,
                    'row' => $this->seat->row,
                    'number' => $this->seat->number,
                    'section' => $this->seat->section,
                    'is_occupied' => $this->seat->is_occupied,
                ] : null;
            }),

            // // Link to download the ticket
            // 'links' => [
            //     'self' => route('tickets.show', $this->id),
            //     'download' => route('tickets.download', $this->id),
            //     'check_in' => $this->when(!$this->is_used, route('tickets.check-in', $this->id)),
            // ],
        ];
    }
}
