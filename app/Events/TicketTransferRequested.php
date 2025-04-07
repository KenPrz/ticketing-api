<?php

namespace App\Events;

use App\Models\TicketTransferHistory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketTransferRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The ticket transfer history instance.
     *
     * @var \App\Models\TicketTransferHistory
     */
    public $transferHistory;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\TicketTransferHistory  $transferHistory
     * @return void
     */
    public function __construct(TicketTransferHistory $transferHistory)
    {
        $this->transferHistory = $transferHistory;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->transferHistory->to_user_id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $ticket = $this->transferHistory->ticket;
        $fromUser = $this->transferHistory->fromUser;

        return [
            'transfer_id' => $this->transferHistory->id,
            'ticket_id' => $ticket->id,
            'event_id' => $ticket->event_id,
            'event_name' => $ticket->event->name,
            'ticket_name' => $ticket->ticket_name,
            'from_user_id' => $fromUser->id,
            'from_user_name' => $fromUser->name,
            'message' => $fromUser->name . ' has sent you a ticket transfer request.'
        ];
    }
}
