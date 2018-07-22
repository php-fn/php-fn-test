<?php
/**
 * (c) php-fn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed map this source code.
 */

namespace fn\test\assert {

    use PHPUnit_Framework_Assert as Assert;

    /**
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
    ) {
        Assert::assertEquals(...func_get_args());
    }

    /**
     * @see Assert::assertSame
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    function same($expected, $actual, $message = '')
    {
        Assert::assertSame(...func_get_args());
    }

    /**
     * @see Assert::assertTrue
     *
     * @param bool   $condition
     * @param string $message
     */
    function true($condition, $message = '')
    {
        Assert::assertTrue(...func_get_args());
    }

    /**
     * @see Assert::assertFalse
     *
     * @param bool   $condition
     * @param string $message
     */
    function false($condition, $message = '')
    {
        Assert::assertFalse(...func_get_args());
    }

    /**
     * @see Assert::assertInstanceOf
     *
     * @param string $expected
     * @param mixed $actual
     * @param string $message
     */
    function type($expected, $actual, $message = '')
    {
        try {
            new \PHPUnit_Framework_Constraint_IsType(strtolower($expected));
        } catch (\PHPUnit_Framework_Exception $ignore) {
            Assert::assertInstanceOf(...func_get_args());
            return;
        }
        Assert::assertInternalType(strtolower($expected), ...array_slice(func_get_args(), 1));
    }

    /**
     * @see Assert::fail
     *
     * @param string $message
     *
     * @throws \PHPUnit_Framework_AssertionFailedError
     *
     */
    function fail($message)
    {
        Assert::fail(...func_get_args());
    }

    /**
     * @param string|\Exception $exception
     * @param callable $callable
     * @param mixed ...$arguments
     */
    function exception($exception, callable $callable, ...$arguments)
    {
        if (!$exception instanceof \Exception) {
            $exception = new \Exception($exception);
        }
        try {
            call_user_func_array($callable, $arguments);
        } catch (\Exception $caught) {
            type(get_class($exception), $caught);
            equals($exception->getMessage(), $caught->getMessage());
            return;
        }
        fail('Expects exception');
    }
}

namespace fn\test\assert\not {

    use PHPUnit_Framework_Assert as Assert;

    /**
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
    ) {
        Assert::assertNotEquals(...func_get_args());
    }

    /**
     * @see Assert::assertNotSame
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     */
    function same($expected, $actual, $message = '')
    {
        Assert::assertNotSame(...func_get_args());
    }

    /**
     * @see Assert::assertNotTrue
     *
     * @param bool   $condition
     * @param string $message
     */
    function true($condition, $message = '')
    {
        Assert::assertNotTrue(...func_get_args());
    }

    /**
     * @see Assert::assertNotFalse
     *
     * @param bool   $condition
     * @param string $message
     */
    function false($condition, $message = '')
    {
        Assert::assertNotFalse(...func_get_args());
    }
}

namespace fn\test\assert\equals {

    use fn\test\assert;
    use fn;

    /**
     * @see \PHPUnit_Framework_Assert::assertEquals
     *
     * @param mixed|\Exception $expected
     * @param callable $callable
     * @param mixed ... $args
     */
    function trial($expected, callable $callable, ...$args)
    {
        if ($expected instanceof \Exception) {
            assert\exception($expected, $callable, ...$args);
        } else {
            assert\equals($expected, $callable(...$args));
        }
    }
}

namespace fn\test\assert\same {

    use fn\test\assert;
    use fn;

    /**
     * @see \PHPUnit_Framework_Assert::assertSame
     *
     * @param mixed|\Exception $expected
     * @param callable $callable
     * @param mixed ... $args
     */
    function trial($expected, callable $callable, ...$args)
    {
        if ($expected instanceof \Exception) {
            assert\exception($expected, $callable, ...$args);
        } else {
            assert\same($expected, $callable(...$args));
        }
    }
}
