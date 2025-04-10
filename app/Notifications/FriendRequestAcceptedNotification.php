<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FriendRequestAcceptedNotification extends Notification
{
    use Queueable;

    protected User $recipient;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Friend Request Accepted')
            ->line($this->recipient->name . ' accepted your friend request')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'recipient_id' => $this->recipient->id,
            'recipient_name' => $this->recipient->name,
            'avatar' => $this->recipient?->avatar,
            'message' => $this->recipient->name . ' accepted your friend request',
            'type' => NotificationType::FRIEND_REQUEST_ACCEPTED->value,
        ];
    }
}