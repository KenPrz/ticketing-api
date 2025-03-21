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
     * Premium seated package with exclusive seating.
     */
    case PREMIUM_SEATED_PACKAGE = 'PREMIUM SEATED PACKAGE';

    /**
     * VIP early entry package with priority access.
     */
    case VIP_EARLY_ENTRY_PACKAGE = 'VIP EARLY ENTRY PACKAGE';

    /**
     * Lower box A premium seating.
     */
    case LOWER_BOX_A_PREMIUM = 'LOWER BOX A PREMIUM';

    /**
     * Lower box A regular seating.
     */
    case LOWER_BOX_A_REGULAR = 'LOWER BOX A REGULAR';

    /**
     * Lower box B premium seating.
     */
    case LOWER_BOX_B_PREMIUM = 'LOWER BOX B PREMIUM';

    /**
     * Lower box B regular seating.
     */
    case LOWER_BOX_B_REGULAR = 'LOWER BOX B REGULAR';

    /**
     * Floor standing area.
     */
    case FLOOR_STANDING = 'FLOOR STANDING';

    /**
     * Upper box premium seating.
     */
    case UPPER_BOX_PREMIUM = 'UPPER BOX PREMIUM';

    /**
     * Upper box regular seating.
     */
    case UPPER_BOX_REGULAR = 'UPPER BOX REGULAR';

    /**
     * General admission ticket type.
     */
    case GENERAL_ADMISSION = 'GENERAL ADMISSION';

    /**
     * Get list of all ticket types.
     *
     * @return array<TicketType>
     */
    public static function list(): array
    {
        return [
            self::GENERAL_ADMISSION,
            self::UPPER_BOX_REGULAR,
            self::UPPER_BOX_PREMIUM,
            self::FLOOR_STANDING,
            self::LOWER_BOX_B_REGULAR,
            self::LOWER_BOX_B_PREMIUM,
            self::LOWER_BOX_A_REGULAR,
            self::LOWER_BOX_A_PREMIUM,
            self::VIP_EARLY_ENTRY_PACKAGE,
            self::PREMIUM_SEATED_PACKAGE,
        ];
    }
}