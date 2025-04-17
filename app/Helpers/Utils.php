<?php

use Carbon\Carbon;

if (! function_exists('adjustEndOfMonth')) {
    function adjustEndOfMonth(int $day, int $month, int $year): Carbon
    {
        $date    = Carbon::createFromDate($year, $month, 1);
        $lastDay = $date->endOfMonth()->day;

        if ($day > $lastDay) {
            $day = $lastDay;
        }

        return Carbon::createFromDate($year, $month, $day);

    }
}
