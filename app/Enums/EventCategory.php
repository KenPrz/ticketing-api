<?php

namespace App\Enums;

/**
 * Ticket Type Enumeration
 * 
 * Represents the different types of tickets available in the system.
 */
enum EventCategory: string
{
    /**
     * Music event category.
     * 
     * @var string
     */
    case CONCERT = 'CONCERT';

    /**
     * Sports event category.
     * 
     * @var string
     */
    case SPORTS = 'SPORTS';

    /**
     * Kids event category.
     * 
     * @var string
     */
    case KIDS = 'KIDS';

    /**
     * Arts and Culture event category.
     * 
     * @var string
     */
    case ARTS = 'ARTS';

    /**
     * Get list of all ticket types.
     *
     * @return array<TicketType>
     */
    public static function list(): array
    {
        return [
            self::KIDS,
            self::SPORTS,
            self::CONCERT,
        ];
    }
}