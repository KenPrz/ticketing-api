<?php

namespace App\Events;

use App\Models\UserFriend;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendRequestAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public UserFriend $userFriend;
    public User $sender;
    public User $recipient;

    /**
     * Create a new event instance.
     */
    public function __construct(UserFriend $userFriend, User $sender, User $recipient)
    {
        $this->userFriend = $userFriend;
        $this->sender = $sender;
        $this->recipient = $recipient;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->sender->id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->userFriend->id,
            'recipient' => [
                'id' => $this->recipient->id,
                'name' => $this->recipient->name,
                'avatar' => $this->recipient->avatar,
                'time' => $this->userFriend->created_at->diffForHumans(),
            ],
        ];
    }
}