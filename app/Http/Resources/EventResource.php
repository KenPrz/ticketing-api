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
            'images' => [
                'banner' => $this->banner?->image_url,
                'seat_plan' => $this->seatPlanImage?->image_url,
                'thumbnail' => $this->thumbnail?->image_url,
                'venue' => $this->venueImage?->image_url,
                'gallery' => $this->gallery?->pluck('image_url')->toArray(),
                'merchandise' => $this?->fetchMerch(),
            ],
            'date' => DateFormatterHelper::dayShort($this->date),
            'isBookmarked' => $this->isBookmarked,
            'category' => $this->category->name,
            'formattedDate' => DateFormatterHelper::dayFull($this->date),
            'time' => $this->time,
            'venue' => $this->venue,
            'city' => $this->city,
            'organizer' => $this?->organizer?->name,
            'priceRange' => $this->fetchPriceRange(),
            'ticket_tiers' => $this->ticketTiers?->map(fn($tier) => [
                'id' => $tier->id,
                'description' => $tier->ticket_desc,
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

        if(empty($prices)) {
            return '₱0.00';
        }

        $minPrice = min($prices);
        $maxPrice = max($prices);

        return $minPrice === $maxPrice
            ? '₱' . number_format($minPrice, 2)
            : '₱' . number_format($minPrice, 2) . ' - ₱' . number_format($maxPrice, 2);
    }

    private function fetchMerch(): array
    {
        return $this->merchandise->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'image' => $item->image ? $item->image->image_url : null,
                'description' => $item->description,
                'stock' => $item->stock,
            ];
        })->toArray();
    }
}