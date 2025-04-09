<?php

namespace App\Enums;

enum FriendStatus:string 
{
    /**
     * Represents the status of a friend request that is pending.
     *
     * @var string
     */
    case PENDING = 'PENDING';
    
    /**
     * Represents the status of a friend request that has been accepted.
     * 
     * @var string
     */
    case ACCEPTED = 'ACCEPTED';

    /**
     * Represents the status of a friend request that has been rejected.
     * 
     * @var string
     */
    case REJECTED = 'REJECTED';

    /**
     * Represents the status of a friend request that has been blocked.
     * 
     * @var string
     */
    case BLOCKED = 'BLOCKED';

    /**
     * Get list of all friend statuses.
     *
     * @return array<FriendStatus>
     */
    public static function list(): array
    {
        return [
            self::PENDING,
            self::ACCEPTED,
            self::REJECTED,
            self::BLOCKED,
        ];
    }
}