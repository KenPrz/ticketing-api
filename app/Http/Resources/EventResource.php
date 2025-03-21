<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\DateFormatterHelper;

class EventResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'date' => DateFormatterHelper::dayShort($this->date),
            'formattedDate' => DateFormatterHelper::dayFull($this->date),
            'time' => $this->time,
            'venue' => $this->venue,
            'city' => $this->city,
            'organizer' => $this->organizer->name,
            'priceRange' => $this->fetchPriceRange(),
            'ticket_tiers' => $this->ticketTiers->map(fn($tier) => [
                'id' => $tier->id,
                'tier_name' => $tier->tier_name,
                'price' => $tier->price,
            ]),
        ];
    }

    /**
     * Fetch the price range of the event.
     *
     * @return string The price range of the event.
     */
    private function fetchPriceRange(): string
    {
        $prices = $this->ticketTiers->pluck('price')->toArray();
        $minPrice = min($prices);
        $maxPrice = max($prices);

        return $minPrice === $maxPrice
            ? '₱' . number_format($minPrice, 2)
            : '₱' . number_format($minPrice, 2) . ' - ₱' . number_format($maxPrice, 2);
    }
}
