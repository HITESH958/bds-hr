<?php

/**
 * Shared display-formatting helpers for BDS HR.
 * Load with helper('hr') at the top of any view/controller that needs them.
 */

if (! function_exists('format_hours')) {
    /**
     * Turns a decimal hour value (e.g. 2.25) into a readable duration
     * (e.g. "2h 15m"), rather than showing raw decimals to users.
     */
    function format_hours(float $hours): string
    {
        $totalMinutes = (int) round($hours * 60);
        $h            = intdiv($totalMinutes, 60);
        $m            = $totalMinutes % 60;

        if ($h === 0 && $m === 0) {
            return '0m';
        }
        if ($h === 0) {
            return $m . 'm';
        }
        if ($m === 0) {
            return $h . 'h';
        }

        return $h . 'h ' . $m . 'm';
    }
}

if (! function_exists('format_status')) {
    /**
     * Turns a raw status enum value (e.g. "half_day") into a readable
     * label (e.g. "Half Day"), instead of leaking underscores to users.
     */
    function format_status(string $status): string
    {
        return ucwords(str_replace('_', ' ', $status));
    }
}
