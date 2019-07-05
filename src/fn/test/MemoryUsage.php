<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\test;

class MemoryUsage
{
    public const K = 'k';
    public const M = 'm';
    public const G = 'g';

    protected const EXP = [
        self::K => 1,
        self::M => 2,
        self::G => 3,
    ];

    /**
     * @param string $memory
     * @param string|null $unit k|m|g
     * @return float|int
     */
    public static function bytes(string $memory, string $unit = null)
    {
        $bytes = (int)$memory * 1024 ** static::EXP[strtolower($memory)[-1]] ?? 0;
        $bytes = $bytes / 1024 ** static::EXP[$unit[0]] ?? 0;
        return is_int($bytes) ? $bytes : round($bytes, 3);
    }
}
