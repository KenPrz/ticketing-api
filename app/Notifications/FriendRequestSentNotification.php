<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\User;
use App\Models\UserFriend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FriendRequestSentNotification extends Notification
{
    use Queueable;

    protected User $sender;
    protected UserFriend $userFriend;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $sender, UserFriend $userFriend)
    {
        $this->sender = $sender;
        $this->userFriend = $userFriend;
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
            ->subject('New Friend Request')
            ->line('You have received a friend request from ' . $this->sender->name)
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
            'interaction_id' => $this->userFriend->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'avatar' => $this->sender?->avatar,
            'message' => $this->sender->name . ' sent you a friend request',
            'type' => NotificationType::FRIEND_REQUEST_SENT->value,
        ];
    }
}