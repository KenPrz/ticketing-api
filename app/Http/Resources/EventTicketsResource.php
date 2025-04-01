<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventTicketsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Ensure all tickets have necessary relationships loaded
        if (!$this->resource->first()?->relationLoaded('event')) {
            $this->resource->load(['event', 'ticketTier', 'owner', 'purchase.purchaser', 'seat']);
        }

        // Group tickets by event ID
        $groupedByEvent = $this->resource->groupBy(function($ticket) {
            return $ticket->event->id ?? 'no_event';
        });
        
        $events = [];
        
        foreach ($groupedByEvent as $eventId => $eventTickets) {
            // Skip tickets with no event
            if ($eventId === 'no_event') {
                continue;
            }
            
            // Get event details from the first ticket
            $event = $eventTickets->first()->event;
            
            $events[] = [
                'event' => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'banner' => $event->banner?->image_url,
                    'date' => $event->date,
                    'time' => $event->time,
                    'venue' => $event->venue,
                    'city' => $event->city,
                    'description' => $event->description,
                    'thumbnail' => $event->thumbnail?->url,
                ],
                'tickets' => TicketResource::collection($eventTickets),
            ];
        }

        return [
            'data' => $events,
        ];
    }
}