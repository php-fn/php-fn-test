<?php declare(strict_types=1);
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace Php\test\assert {

    use Exception;
    use PHPUnit\Framework\Assert;
    use PHPUnit\Framework\AssertionFailedError;
    use PHPUnit\Framework\Constraint\IsType;

    /**
     * @deprecated
     * @see Assert::assertEquals
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     * @param float  $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    function equals(
        $expected,
        $actual,
        $message = '',
        $delta = 0.0,
        $maxDepth = 10,
        $canonicalize = false,
        $ignoreCase = false
    ): void {
        Assert::assertEquals(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertSame
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    function same($expected, $actual, $message = ''): void
    {
        Assert::assertSame(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertGreaterThan
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     */
    function gt($expected, $actual, string $message = ''): void
    {
        Assert::assertGreaterThan(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertGreaterThanOrEqual
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     */
    function ge($expected, $actual, string $message = ''): void
    {
        Assert::assertGreaterThanOrEqual(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertLessThan
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     */
    function lt($expected, $actual, string $message = ''): void
    {
        Assert::assertLessThan(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertLessThanOrEqual
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     */
    function le($expected, $actual, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertTrue
     *
     * @param bool   $condition
     * @param string $message
     */
    function true($condition, $message = ''): void
    {
        Assert::assertTrue(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertFalse
     *
     * @param bool   $condition
     * @param string $message
     */
    function false($condition, $message = ''): void
    {
        Assert::assertFalse(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::fail
     *
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    function fail($message): void
    {
        Assert::fail(...func_get_args());
    }

    /**
     * @deprecated
     * @param string|Exception $exception
     * @param callable $callable
     * @param mixed ...$arguments
     */
    function exception($exception, callable $callable, ...$arguments): void
    {
        if (!$exception instanceof Exception) {
            $exception = new Exception($exception);
        }
        try {
            call_user_func_array($callable, $arguments);
        } catch (Exception $caught) {
            Assert::assertInstanceOf(get_class($exception), $caught);
            equals($exception->getMessage(), $caught->getMessage());
            return;
        }
        fail('Expects exception');
    }
}

namespace Php\test\assert\not {

    use PHPUnit\Framework\Assert;

    /**
     * @deprecated
     * @see Assert::assertNotEquals
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     * @param float  $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     */
    function equals(
        $expected,
        $actual,
        $message = '',
        $delta = 0.0,
        $maxDepth = 10,
        $canonicalize = false,
        $ignoreCase = false
    ): void {
        Assert::assertNotEquals(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertNotSame
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    function same($expected, $actual, $message = ''): void
    {
        Assert::assertNotSame(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertNotTrue
     *
     * @param bool   $condition
     * @param string $message
     */
    function true($condition, $message = ''): void
    {
        Assert::assertNotTrue(...func_get_args());
    }

    /**
     * @deprecated
     * @see Assert::assertNotFalse
     *
     * @param bool   $condition
     * @param string $message
     */
    function false($condition, $message = ''): void
    {
        Assert::assertNotFalse(...func_get_args());
    }
}

namespace Php\test\assert\equals {

    use Exception;
    use Php\test\assert;
    use Php;

    /**
     * @deprecated
     * @see \PHPUnit\Framework\Assert::assertEquals
     *
     * @param mixed|Exception $expected
     * @param callable $callable
     * @param mixed ... $args
     */
    function trial($expected, callable $callable, ...$args): void
    {
        if ($expected instanceof Exception) {
            assert\exception($expected, $callable, ...$args);
        } else {
            assert\equals($expected, $callable(...$args));
        }
    }
}

namespace Php\test\assert\same {

    use Exception;
    use Php\test\assert;
    use Php;

    /**
     * @deprecated
     * @see \PHPUnit\Framework\Assert::assertSame
     *
     * @param mixed|Exception $expected
     * @param callable $callable
     * @param mixed ... $args
     */
    function trial($expected, callable $callable, ...$args): void
    {
        if ($expected instanceof Exception) {
            assert\exception($expected, $callable, ...$args);
        } else {
            assert\same($expected, $callable(...$args));
        }
    }
}
