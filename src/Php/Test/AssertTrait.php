<?php declare(strict_types=1);
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace Php\Test;

use Closure;
use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint;

/**
 * @uses Assert::assertEquals
 * @method static tryEquals(mixed|Exception $expected, mixed|Closure $actual, string $message = ''): void
 * @uses Assert::assertNotEquals
 * @method static tryNotEquals(mixed|Exception $expected, mixed|Closure $actual, string $message = ''): void
 *
 * @uses Assert::assertSame
 * @method static trySame(mixed|Exception $expected, mixed|Closure $actual, string $message = ''): void
 * @uses Assert::assertNotSame
 * @method static tryNotSame(mixed|Exception $expected, mixed|Closure $actual, string $message = ''): void
 */
trait AssertTrait
{
    protected static function actual($actual, ...$args)
    {
        return $actual instanceof Closure ? $actual(...$args) : $actual;
    }

    public static function __callStatic(string $method, array $args)
    {
        $method = substr($method, 2);
        $expected = $args[0] ?? null;
        $actual = $args[1] ?? null;

        if ($expected instanceof Exception) {
            static::assertException(...$args);
        } else {
            Assert::${"assert$method"}($expected, static::actual($actual), ...array_slice($args, 2));
        }
    }

    /**
     * @param string|Exception $expected
     * @param mixed|Closure    $actual
     * @param string           $message
     * @param mixed            ...$args
     */
    public static function assertException($expected, $actual, string $message = '', ...$args): void
    {
        if (!$expected instanceof Exception) {
            $expected = new Exception($expected);
        }
        try {
            $actual = static::actual($actual, ...$args);
        } catch (Exception $caught) {
            $actual = $caught;
        }
        (new Constraint\Exception(get_class($expected)))->evaluate($actual, $message);
        (new Constraint\ExceptionMessage($expected->getMessage()))->evaluate($actual, $message);
    }
}
