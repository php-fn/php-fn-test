<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\test;

use Generator;
use MathPHP\Statistics\Correlation;

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
     * @var int
     */
    private $step;

    /**
     * @var string
     */
    private $max;

    /**
     * @param string $step
     * @param string $max
     */
    public function __construct(string $step = null, string $max = null)
    {
        $this->step = $step ?? '0.1%';
        $this->max = $max ?? '10%';
    }

    /**
     * @param string $unit
     * @param mixed $limit
     * @param mixed $usage
     * @return array
     */
    public function describe(string $unit = null, string $limit = null, string $usage = null): array
    {
        $unit = $unit ?? self::M;
        $limit = self::bytes($limit ?? ini_get('memory_limit'));
        $usage = self::bytes($usage ?? (string)memory_get_usage());

        return iterator_to_array((function () use ($unit, $limit, $usage) {
            yield 'limit' => self::bytes($limit, $unit) . $unit;
            yield 'usage' => self::bytes($usage, $unit) . $unit;
            yield 'free' => self::bytes($free = $limit - $usage, $unit) . $unit;

            $max = $this->max[-1] === '%' ? ($free * (float)$this->max) / 100 : self::bytes($this->max);
            yield 'max' => $this->max;
            yield 'max.memory' => self::bytes($max, $unit) . $unit;

            $step = $this->step[-1] === '%' ? ($max * (float)$this->step) / 100 : self::bytes($this->step);
            yield 'step' => $this->step;
            yield 'step.memory' => self::bytes($step, $unit) . $unit;
            yield 'step.count' => (int)round($max / $step);
        })());
    }

    /**
     * @param callable $factory
     * @param bool $simulateLeak
     * @return Generator
     */
    public function __invoke(callable $factory, bool $simulateLeak = false): Generator
    {
        static $repeat;
        $repeat ?: $repeat = static function (int $multiplier = 1024) {
            return str_repeat('b', $multiplier);
        };
        ['step.count' => $stepCount, 'usage' => $startUsage, 'step.memory' => $stepMemory] = $this->describe('');
        $data = [];
        for ($i = 0; $i < $stepCount; ++$i) {
            $simulateLeak || $data = [];
            $memoryUsage = self::bytes(memory_get_usage());
            do {
                $data[] = $factory($repeat);
                $stopUsage = self::bytes(memory_get_usage());
                $stepDiff = $stopUsage - $memoryUsage;
            } while ($stepDiff < $stepMemory);
            yield $stopUsage - $startUsage;
        }
        unset($data);
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param callable $factory
     * @param string|null $step
     * @param string|null $max
     * @return float
     */
    public static function timeCorrelation(callable $factory, string $step = null, string $max = null): float
    {
        $mu = new static($step, $max);
        $data = iterator_to_array($mu($factory));
        return Correlation::kendallsTau(array_keys($data), $data);
    }

    /**
     * @param string|int|float $memory
     * @param string|null $unit k|m|g
     * @return int|float
     */
    public static function bytes(string $memory, string $unit = null)
    {
        $bytes = (float)$memory * 1024 ** (static::EXP[strtolower($memory)[-1]] ?? 0);
        $float = round($bytes / 1024 ** (static::EXP[$unit[0] ?? null] ?? 0), 3);
        return $float <=> ($int = (int)$float) ? $float : $int;
    }
}
