<?php

namespace Calculated;

class Calculated
{
    public static function calculateCost(int $length, int $width): int
    {
        $currentMonth = (int)date('n');
        return $length * $width * $currentMonth;
    }
}