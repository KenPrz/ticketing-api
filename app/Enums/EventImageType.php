<?php

namespace App\Enums;

enum EventImageType: string
{
    /**
     * Represents the event's main image.
     */
    case BANNER = 'BANNER';

    /**
     * Represents the event's thumbnail image.
     */
    case THUMBNAIL = 'THUMBNAIL';

    /**
     * Represents the event's gallery image.
     */
    case GALLERY = 'GALLERY';

    /**
     * Represents the event's venue image.
     */
    case VENUE = 'VENUE';

    /**
     * Represents the event's merchandise image.
     */
    case MERCHANDISE = 'MERCHANDISE';

    /**
     * Get list of all ticket types.
     *
     * @return array<EventImageType>
     */
    public static function list(): array
    {
        return [
            self::BANNER,
            self::THUMBNAIL,
            self::GALLERY,
            self::VENUE,
            self::MERCHANDISE,
        ];
    }
}