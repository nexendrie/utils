<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * Numbers
 *
 * @author Jakub Konečný
 */
final class Numbers
{
    private function __construct()
    {
    }

    /**
     * Ensure that a number is within boundaries
     */
    public static function clamp(int $number, int $min, int $max): int
    {
        return min(max($number, $min), $max);
    }

    /**
     * Check whether a number is within boundaries
     */
    public static function isInRange(int $number, int $min, int $max): bool
    {
        return ($number >= $min && $number <= $max);
    }
}
