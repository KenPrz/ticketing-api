<?php

namespace App\Enums;

/**
 * Ticket Type Enumeration
 * 
 * Represents the different types of tickets available in the system.
 */
enum TicketType: string
{
    /**
     * General admission ticket type.
     */
    case GENERAL_ADMISSION = 'general_admission';

    /**
     * VIP ticket type with special privileges.
     */
    case VIP = 'vip';

    /**
     * Early bird ticket type with early booking benefits.
     */
    case EARLY_BIRD = 'EARLY BIRD';

    /**
     * Group ticket type for multiple attendees.
     */
    case GROUP = 'GROUP';

    /**
     * Standard ticket type.
     */
    case STANDARD = 'STANDARD';

    /**
     * Premium ticket type with additional benefits.
     */
    case PREMIUM = 'PREMIUM';

    /**
     * Get list of all ticket types.
     *
     * @return array<TicketType>
     */
    public static function list(): array
    {
        return [
            self::GENERAL_ADMISSION,
            self::VIP,
            self::EARLY_BIRD,
            self::GROUP,
            self::STANDARD,
            self::PREMIUM,
        ];
    }
}