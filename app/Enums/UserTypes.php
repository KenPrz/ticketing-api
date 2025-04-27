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

    /**
     * Get the list of all user types.
     *
     * @return array<string>
     */
    public static function list(): array
    {
        return [
            self::CLIENT,
            self::ORGANIZER,
            self::ADMIN,
        ];
    }

    /**
     * Get the list of user types that are exposed to the client.
     *
     * @return array<string>
     */
    public static function exposedUserTypes(): array
    {
        return [
            self::CLIENT->value,
            self::ORGANIZER->value,
        ];
    }
}
