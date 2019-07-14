<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\test;

use MathPHP\Statistics\Correlation;
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
            '512.5 M => 512 * 1024 ** 2 + 512 * 1024' => [512 * 1024 ** 2 + 512 * 1024, '512.5 M'],
            '1.001g -> k => 512 * 1024 ** 2 + 512 * 1024' => [1.001 * 1024 ** 2, '1.001g', MemoryUsage::K],
            '100k -> m => 0.098' => [0.098, '100k', MemoryUsage::M],
            '0.098m -> k => 100.252' => [100.352, '0.098m', MemoryUsage::K],
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
        $this->assertSame($expected, MemoryUsage::bytes(...$args));
    }

    /**
     * @return array
     */
    public static function providerDescribe(): array
    {
        return [
            '512m-64m default' => [[
                'limit' => '512m',
                'usage' => '64m',
                'free' => '448m',
                'max' => '10%',
                'max.memory' => '44.8m',
                'step' => '0.1%',
                'step.memory' => '0.045m',
                'step.count' => 1000,
            ], null, null, null, '512m', '64m'],

            '512m-64m max=128m' => [[
                'limit' => '512m',
                'usage' => '64m',
                'free' => '448m',
                'max' => '128m',
                'max.memory' => '128m',
                'step' => '0.1%',
                'step.memory' => '0.128m',
                'step.count' => 1000,
            ], null, '128m', null, '512m', '64m'],

            '512m-64m step=100k' => [[
                'limit' => '512m',
                'usage' => '64m',
                'free' => '448m',
                'max' => '10%',
                'max.memory' => '44.8m',
                'step' => '100k',
                'step.memory' => '0.098m',
                'step.count' => 459,
            ], '100k', null, null, '512m', '64m'],

            '64m-48m step=1% max=50% => b' => [[
                'limit' => (string)(64 * 1024 ** 2),
                'usage' => (string)(48 * 1024 ** 2),
                'free' => (string)(16 * 1024 ** 2),
                'max' => '50%',
                'max.memory' => (string)(8 * 1024 ** 2),
                'step' => '1%',
                'step.memory' => '83886.08',
                'step.count' => 100,
            ], '1%', '50%', '', '64m', '48m'],
        ];
    }

    /**
     * @dataProvider providerDescribe
     * @covers ::describe
     * @param $expected
     * @param mixed ...$args
     */
    public function testDescribe($expected, ...$args): void
    {
        $this->assertSame(
            $expected,
            (new MemoryUsage(...$args))->describe(... array_slice($args, 2))
        );
    }

    /**
     * @covers ::describe
     */
    public function testDescribeDefaults(): void
    {
        $result = (new MemoryUsage)->describe();

        $this->assertSame('10%', $result['max']);
        $this->assertSame('0.1%', $result['step']);
        $this->assertSame(1000, $result['step.count']);
        $this->assertSame(
            MemoryUsage::bytes(ini_get('memory_limit'), MemoryUsage::M) . MemoryUsage::M,
            $result['limit']
        );
        $this->assertSame(
            (int)(MemoryUsage::bytes(memory_get_usage(), MemoryUsage::M) . MemoryUsage::M),
            (int)$result['usage']
        );
    }

    /**
     * @covers ::__invoke
     * @covers ::timeCorrelation
     */
    public function testInvoke(): void
    {
        $mu = new MemoryUsage;
        $factory = static function (callable $fill) {
            return (object)[$fill(1024 * 100)];
        };
        $this->assertCount(1000, $ok = iterator_to_array($mu($factory)));
        $this->assertCount(1000, $nok = iterator_to_array($mu($factory, true)));
        $this->assertLessThan(0.95, Correlation::kendallsTau(array_keys($ok), $ok));
        $this->assertGreaterThan(0.95, Correlation::kendallsTau(array_keys($nok), $nok));

        $this->assertLessThan(0.95, MemoryUsage::timeCorrelation($factory));
        $this->assertGreaterThan(0.95, MemoryUsage::timeCorrelation(static function (callable $fill) {
            static $cache = [];
            return $cache[] = $fill();
        }));
    }
}
