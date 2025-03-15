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
        ];
    }
}
