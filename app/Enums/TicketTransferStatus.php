<?php

namespace App\Enums;

/**
 * Ticket Type Enumeration
 * 
 * Represents the different types of tickets available in the system.
 */
enum TicketTransferStatus: string
{
    /**
     * When Ticket is transferred to another user.
     * 
     * @var string
     */
    case TRANSFERRED = 'TRANSFERRED';

    /**
     * Pending transfer status.
     * 
     * @var string
     */
    case PENDING = 'PENDING';

    /**
     * When Ticket is rejected by the receiver.
     * 
     * @var string
     */
    case REJECTED = 'REJECTED';
    
    /**
     * When the trasfer is cancelled by the sender.
     *
     * @return string
     */
    case CANCELLED = 'CANCELLED';
    

    /**
     * Get list of all ticket types.
     *
     * @return array<TicketTransferStatus>
     */
    public static function list(): array
    {
        return [
            self::TRANSFERRED,
            self::PENDING,
            self::REJECTED,
            self::CANCELLED,
        ];
    }
}