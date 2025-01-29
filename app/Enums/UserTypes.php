<?php

namespace App\Enums;

/**
 * Enum for user role types.
 */
enum UserTypes
{
    /**
     * Client User
     *
     * @var int
     */
    public const CLIENT = 'CLIENT';

    /**
     * Organizer User
     *
     * @var int
     */
    public const ORGANIZER = 'ORGANIZER';

    /**
     * Admin User
     *
     * @var int
     */
    public const ADMIN = 'ADMIN';

    /**
     * Represents the list of user types.
     */
    public const LIST = [
        self::CLIENT,
        self::ORGANIZER,
        self::ADMIN,
    ];
}
