<?php

namespace App\Enums;

enum MerchandiseStatus: string
{
    /**
     * Represents the merchandise's available status.
     */
    case AVAILABLE = 'AVAILABLE';

    /**
     * Represents the merchandise's sold out status.
     */
    case SOLD_OUT = 'SOLD_OUT';

    /**
     * Represents the merchandise's restocking status.
     */
    case RESTOCKING = 'RESTOCKING';


    /**
     * Get list of all ticket types.
     *
     * @return array<MerchandiseStatus>
     */
    public static function list(): array
    {
        return [
            self::AVAILABLE,
            self::SOLD_OUT,
            self::RESTOCKING,
        ];
    }
}