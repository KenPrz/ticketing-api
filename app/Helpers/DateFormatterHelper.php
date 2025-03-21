<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateFormatterHelper
{
    /**
     * Format the date to a short format.
     *
     * @param  string  $date  The date to format
     *
     * @return string  The formatted date
     */
    public static function dayFull(string $date): string
    {
        return Carbon::parse($date)->format('l, F j, Y');
    }

    /**
     * Format the date to a short format.
     *
     * @param  string  $date  The date to format
     *
     * @return string  The formatted date
     */
    public static function dayShort(string $date): string
    {
        return Carbon::parse($date)->format('M j');
    }
}