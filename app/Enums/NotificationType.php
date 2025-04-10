<?php

namespace App\Enums;

enum NotificationType:string
{
    /**
     * Notification type for new messages.
     * 
     * @var string
     */
    case MESSAGE = 'MESSAGE';

    /**
     * Notification type for friend requests.
     * 
     * @var string
     */
    
    case FRIEND_REQUEST_SENT = 'FRIEND_REQUEST_SENT';

    /**
     * Notification type for friend request accepted.
     * 
     * @var string
     */
    case FRIEND_REQUEST_ACCEPTED = 'FRIEND_REQUEST_ACCEPTED';

    /**
     * Get list of all notification types.
     *
     * @return array<NotificationType>
     */
    public static function list(): array
    {
        return [
            self::MESSAGE,
            self::FRIEND_REQUEST_SENT,
            self::FRIEND_REQUEST_ACCEPTED,
        ];
    }
}