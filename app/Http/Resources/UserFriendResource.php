<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFriendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $currentUserId = $request->user()->id;
        
        // Determine which user is the actual friend (not the current user)
        if ($this->user_id == $currentUserId) {
            // Current user is the initiator, load friend
            $actualFriend = $this->whenLoaded('friend', function () {
                return new UserResource($this->friend);
            });
        } else {
            // Current user is the friend, load initiator as "friend"
            $actualFriend = $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            });
        }
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'friend_id' => $this->friend_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'friend' => $actualFriend
        ];
    }
}