<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\PostContext;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post_id' => $this->id,
            'user_id' => $this->user_id,
            'can_edit' => $this->user_id === $request->user()->id,
            'header' => "{$this->user->name} " . PostContext::getPostTag($this->post_context),
            'content' => $this->content,
            'price' => (int) $this->price,
            'post_context' => $this->post_context,
            'event_id' => $this->event_id,
            'upvotes' => $this->upvotes,
            'downvotes' => $this->downvotes,
            'is_upvoted' => $this->isUpvotedBy($request->user()),
            'is_downvoted' => $this->isDownvotedBy($request->user()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar_url,
            ],
            'ticket' => $this->whenLoaded('ticket', function () {
                $this->ticket->load('seat');
                return [
                    'id' => $this->id,
                    'ticket_name' => $this->ticket->ticket_name,
                    'ticket_type' => $this->ticket->ticket_type,
                    'ticket_desc' => $this->ticket->ticket_desc,
                    'ticket_tier' => $this->ticket->ticketTier,
                    'seat' => $this->whenLoaded('ticket', function () {
                        return [
                            'id' => $this->seat?->id,
                            'row' => $this->seat?->row,
                            'number' => $this->seat?->number,
                            'section' => $this->seat?->section,
                        ];
                    }),
                ];
            }),
            'event' => $this->whenLoaded('event', function () {
                return [
                    'id' => $this->event?->id,
                    'name' => $this->event?->name,
                    'date' => $this->event?->date,
                    'time' => $this->event?->time,
                    'venue' => $this->event?->venue,
                    'city' => $this->event?->city,
                    'description' => $this->event?->description,
                    'banner' => $this->event?->banner->image_url,
                ];
            }),
        ];
    }
}
