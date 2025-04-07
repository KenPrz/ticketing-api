<?php

namespace App\Notifications;

use App\Models\TicketTransferHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TicketTransferRequest extends Notification
{
    use Queueable;

    /**
     * The ticket transfer history instance.
     *
     * @var \App\Models\TicketTransferHistory
     */
    protected $transferHistory;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\TicketTransferHistory  $transferHistory
     * @return void
     */
    public function __construct(TicketTransferHistory $transferHistory)
    {
        $this->transferHistory = $transferHistory;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $ticket = $this->transferHistory->ticket;
        $fromUser = $this->transferHistory->fromUser;
        
        $acceptUrl = URL::temporarySignedRoute(
            'tickets.transfer.accept',
            now()->addDays(7),
            ['transferId' => $this->transferHistory->id]
        );
        
        $rejectUrl = URL::temporarySignedRoute(
            'tickets.transfer.reject',
            now()->addDays(7),
            ['transferId' => $this->transferHistory->id]
        );

        return (new MailMessage)->view(
            'emails/ticket-transfer', 
            [
                'fromUser' => $fromUser,
                'ticket' => $ticket,
                'acceptUrl' => $acceptUrl,
                'rejectUrl' => $rejectUrl
            ],
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
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