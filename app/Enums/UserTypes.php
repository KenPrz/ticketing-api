<?php

namespace App\Enums;

/**
 * Enum for user role types.
 */
enum UserTypes: string
{
    /**
     * Client User
     *
     * @var string
     */
    case CLIENT = 'CLIENT';

    /**
     * Organizer User
     *
     * @var string
     */
    case ORGANIZER = 'ORGANIZER';

    /**
     * Admin User
     *
     * @var string
     */
    case ADMIN = 'ADMIN';

    public static function list(): array
    {
        return [
            self::CLIENT,
            self::ORGANIZER,
            self::ADMIN,
        ];
    }
}
