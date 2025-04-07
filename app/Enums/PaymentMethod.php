<?php

namespace App\Enums;

/**
 * Payment Method Enumeration
 *
 * Represents the different payment methods available in the system.
 */
enum PaymentMethod: string
{
    /**
     * Payment method using debit card.
     * 
     * @var string
     */

    case DEBIT_CARD = 'debit_card';

    /**
     * Payment method using GCash.
     * 
     * @var string
     */

    case GCASH = 'gcash';

    /**
     * Payment method using Maya.
     * 
     * @var string
     */
    case MAYA = 'maya';

    /**
     * Get list of all payment methods.
     *
     * @return array<PaymentMethod>
     */
    public static function list(): array
    {
        return [
            self::DEBIT_CARD,
            self::GCASH,
            self::MAYA,
        ];
    }
}
