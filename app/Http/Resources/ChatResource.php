<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserChatResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();
        $otherUser = $this->getOtherUser($currentUser->id);
        
        return [
            'id' => $this->id,
            'other_user' => new UserChatResource($otherUser),
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'unread_count' => $this->unreadCount($currentUser->id),
            'last_message' => $this->when($this->lastMessage(), function () {
                return new MessageResource($this->lastMessage());
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}