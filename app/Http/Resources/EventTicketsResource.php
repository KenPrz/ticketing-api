<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
            $this->resource->load(['event', 'ticketTier', 'owner', 'purchase.purchaser', 'seat', 'transferStatus']);
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

        // Get current date for comparison
        $currentDate = Carbon::now()->startOfDay();
        
        // Sort events by date, prioritizing future events
        usort($events, function($a, $b) use ($currentDate) {
            $dateA = Carbon::parse($a['event']['date']);
            $dateB = Carbon::parse($b['event']['date']);
            
            $aIsFuture = $dateA->greaterThanOrEqualTo($currentDate);
            $bIsFuture = $dateB->greaterThanOrEqualTo($currentDate);
            
            // If one is future and one is past, prioritize future
            if ($aIsFuture && !$bIsFuture) {
                return -1; // a comes first
            }
            if (!$aIsFuture && $bIsFuture) {
                return 1; // b comes first
            }
            
            // If both are future or both are past, sort by date
            return $dateA->lt($dateB) ? -1 : 1;
        });

        return [
            'data' => $events,
        ];
    }
}