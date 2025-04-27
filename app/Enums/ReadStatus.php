<?php

namespace App\Enums;

/**
 * Ticket Type Enumeration
 * 
 * Represents the different types of tickets available in the system.
 */
enum ReadStatus: string
{
    /**
     * Read Message status.
     * 
     * @var string
     */
    case READ = 'READ';

    /**
     * Unread Message status.
     * 
     * @var string
     */
    case UNREAD = 'UNREAD';

    /**
     * Get list of all ticket types.
     *
     * @return array<ReadStatus>
     */
    public static function list(): array
    {
        return [
            self::READ,
            self::UNREAD,
        ];
    }

    /**
     * Get all possible values of the enum.
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}