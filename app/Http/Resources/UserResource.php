<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();
        $user = User::find($this->id);

        // Friendship status flags
        $isFriends = false;
        $hasPendingRequestFrom = false;
        $hasPendingRequestTo = false;
        $friendshipStatus = 'NOT_FRIENDS';

        if ($currentUser && $user && $currentUser->id !== $user->id) {
            $isFriends = $currentUser->isFriendsWith($user);
            $hasPendingRequestFrom = $currentUser->hasPendingRequestFrom($user);
            $hasPendingRequestTo = $currentUser->hasPendingRequestTo($user);
            $friendshipStatus = $currentUser->getFriendshipStatusWith($user);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'avatar' => $this->avatarUrl ?? null,
            'user_type' => $this->user_type,
            
            // Friendship status
            'is_friends' => $isFriends,
            'has_pending_request_from' => $hasPendingRequestFrom,
            'has_pending_request_to' => $hasPendingRequestTo,
            'friendship_status' => $friendshipStatus,
        ];
    }
}