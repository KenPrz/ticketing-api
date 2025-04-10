<?php

namespace App\Listeners;

use App\Events\FriendRequestSent;
use App\Notifications\FriendRequestSentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FriendRequestSentListener
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(FriendRequestSent $event): void
    {
        // Send notification to the recipient
        $event->recipient->notify(new FriendRequestSentNotification($event->sender, $event->userFriend));
    }
}