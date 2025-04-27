<?php

namespace App\Enums;

/**
 * Ticket Type Enumeration
 * 
 * Represents the different types of tickets available in the system.
 */
enum PostContext: string
{
    /**
     * Experience post context.
     * 
     * @var string
     */
    case EXPERIENCE = 'EXPERIENCE';

    /**
     * Sell post context.
     * 
     * @var string
     */
    case SELL = 'SELL';

    /**
     * Get the label for the given post context.
     * 
     * @param PostContext $context
     * @return string
     */
    case NORMAL = 'NORMAL';

    /**
     * Get list of all ticket types.
     *
     * @return array<PostContext>
     */
    public static function list(): array
    {
        return [
            self::EXPERIENCE,
            self::SELL,
            self::NORMAL,
        ];
    }

    /**
     * Get the post tag for the given post context.
     * 
     * @param PostContext $context
     * @return string
     */
    public static function getPostTag(PostContext $context): string
    {
        return match ($context) {
            self::EXPERIENCE => 'Has shared an experience',
            self::SELL => 'Is selling a ticket',
            self::NORMAL => 'Has posted',
            default => null,
        };
    }
}