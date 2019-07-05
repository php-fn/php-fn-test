<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\test;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass MemoryUsage
 */
class MemoryUsageTest extends TestCase
{
    /**
     * @return array
     */
    public static function providerBytes(): array
    {
        return [
            '0 => 0' => [0, 0],
            '1 => 1' => [1, 1],
            '1 b => 1' => [1, '1 b'],
            '1b => 1' => [1, '1b'],
            '1k => 1024' => [1024, '1k'],
            '1M => 1024 ** 2' => [1024 ** 2, '1M'],
            '1 G => 1024 ** 3' => [1024 ** 3, '1 G'],
            '512.5 M => 512 ** 2' => [512 * 1024 ** 2, '512.5 M'],
            '0 -> k => 0' => [0, 0, MemoryUsage::K],
            '0 -> M => 0' => [0, 0, 'M'],
            '0 -> Gig => 0' => [0, 0, 'Gig'],
            '1k -> b => 1024' => [1024, '1k', 'b'],
            '1m -> k => 1024' => [1024, '1m', MemoryUsage::K],
            '1m -> b => 1024 ** 2' => [1024 ** 2, '1m'],
            '1g -> m => 1024' => [1024, '1g', MemoryUsage::M],
            '1g -> k => 1024 ** 2' => [1024 ** 2, '1g', MemoryUsage::K],
            '1g -> b => 1024 ** 3' => [1024 ** 3, '1g'],
            '1k -> m => 0.001' => [0.001, '1k', MemoryUsage::M],
            '512k -> m => 0.5' => [0.5, '512k', MemoryUsage::M],
            '1024k -> g => 0.001' => [0.001, '1024k', MemoryUsage::G],
        ];
    }

    /**
     * @dataProvider providerBytes
     * @covers ::bytes
     *
     * @param $expected
     * @param mixed ...$args
     */
    public function testBytes($expected, ...$args): void
    {
        assert\same($expected, MemoryUsage::bytes(...$args));
    }
}
