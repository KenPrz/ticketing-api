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
    
    case FRIEND_REQUEST = 'FRIEND_REQUEST';

    /**
     * Get list of all notification types.
     *
     * @return array<NotificationType>
     */
    public static function list(): array
    {
        return [
            self::MESSAGE,
            self::FRIEND_REQUEST,
        ];
    }
}