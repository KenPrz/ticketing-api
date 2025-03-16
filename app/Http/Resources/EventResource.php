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
        ];
    }

    /**
     * Fetch the price range of the event.
     *
     * @return string The price range of the event.
     */
    private function fetchPriceRange(): string
    {
        $minPrice = $this->tickets->min('price');
        $maxPrice = $this->tickets->max('price');

        return $minPrice === $maxPrice
            ? '$' . number_format($minPrice, 2)
            : '$' . number_format($minPrice, 2) . ' - $' . number_format($maxPrice, 2);
    }
}
