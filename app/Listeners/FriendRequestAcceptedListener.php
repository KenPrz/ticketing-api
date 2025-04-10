<?php

namespace App\Listeners;

use App\Events\FriendRequestAccepted;
use App\Notifications\FriendRequestAcceptedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FriendRequestAcceptedListener
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(FriendRequestAccepted $event): void
    {
        // Send notification to the original sender of the friend request
        $event->sender->notify(new FriendRequestAcceptedNotification($event->recipient));
    }
}