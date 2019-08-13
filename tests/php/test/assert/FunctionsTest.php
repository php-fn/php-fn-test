<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace php\test\assert;

use Exception;
use LogicException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Exception as FrameworkException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 */
class FunctionsTest extends TestCase
{
    /**
     */
    public function testPositive(): void
    {
        equals(1, '1');
        same(1, 1);
        true(true);
        false(false);

        not\equals(1, 0);
        not\same(1, '1');
        not\true(false);
        not\false(true);

        type('array', []);
        type(self::class, $this);

        try {
            fail('message');
            self::fail();
        } catch (AssertionFailedError $exception) {
            self::assertEquals('message', $exception->getMessage());
        }

        exception('message', static function (FunctionsTest $arg) {
            fail('message');
        }, $this);

        exception(
            $exception = new Exception('exception'),
            static function (FunctionsTest $arg1, FunctionsTest $arg2) use ($exception) {
                throw $exception;
            },
            $this,
            $this
        );

        equals\trial(1, static function () {
            return '1';
        });

        equals\trial($exception = new Exception('message'), static function (Exception $exception) {
            throw $exception;
        }, $exception);

        same\trial(1, static function() {
            return 1;
        });

        same\trial($exception = new Exception('message'), static function (Exception $exception) {
            throw $exception;
        }, $exception);

        gt(0.0098, 0.0099);
        ge(0.0098, 0.0099);
        ge(0.0098, 0.0098);
        lt(0.0098, 0.0097);
        le(0.0098, 0.0097);
        le(0.0098, 0.0098);
    }

    /**
     */
    public function testNegative(): void
    {
        try {
            equals(1, 2);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 2 matches expected 1.', $actual->getMessage());
        }

        try {
            same(1, '1');
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame("Failed asserting that '1' is identical to 1.", $actual->getMessage());
        }

        try {
            true(false);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that false is true.', $actual->getMessage());
        }

        try {
            false(true);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that true is false.', $actual->getMessage());
        }

        try {
            not\equals(1, '1');
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame("Failed asserting that '1' is not equal to 1.", $actual->getMessage());
        }

        try {
            not\same(1, 1);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 1 is not identical to 1.', $actual->getMessage());
        }

        try {
            not\true(true);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that true is not true.', $actual->getMessage());
        }

        try {
            not\false(false);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that false is not false.', $actual->getMessage());
        }

        try {
            type('not-a-type', $this);
            self::fail();
        } catch (FrameworkException $actual) {
            self::assertSame(
                'Argument #1 (No Value) of PHPUnit\\Framework\\Assert::assertInstanceOf() must be a class or interface name',
                $actual->getMessage()
            );
        }

        try {
            type('array', null);
            self::fail();
        } catch (FrameworkException $actual) {
            self::assertSame('Failed asserting that null is of type "array".', $actual->getMessage());
        }

        try {
            exception(null, static function () {});
            self::fail();
        } catch (AssertionFailedError $actual) {
            self::assertSame('Expects exception', $actual->getMessage());
        }

        try {
            exception(new LogicException, static function () {
                throw new RuntimeException('');
            });
            self::fail();
        } catch (ExpectationFailedException $actual) {
            self::assertSame(
                'Failed asserting that RuntimeException Object (...) is an instance of class "LogicException".',
                $actual->getMessage()
            );
        }

        try {
            exception('expected', static function () {
                fail('actual');
            });
            self::fail();
        } catch (ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that two strings are equal.', $actual->getMessage());
        }

        try {
            equals\trial(1, static function () {
                return 2;
            });
            self::fail();
        } catch (ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 2 matches expected 1.', $actual->getMessage());
        }

        try {
            equals\trial(new Exception('message'), static function () {
                fail('actual');
            });
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that two strings are equal.', $actual->getMessage());
        }

        try {
            same\trial(1, static function () {
                return '1';
            });
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame("Failed asserting that '1' is identical to 1.", $actual->getMessage());
        }

        try {
            same\trial(new Exception('message'), static function () {
                fail('actual');
            });
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that two strings are equal.', $actual->getMessage());
        }

        try {
            gt(0.0098, 0.0098);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 0.0098 is greater than 0.0098.', $actual->getMessage());
        }

        try {
            gt(0.0098, 0.0097);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 0.0097 is greater than 0.0098.', $actual->getMessage());
        }

        try {
            ge(0.0098, 0.0097);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 0.0097 is equal to 0.0098 or is greater than 0.0098.', $actual->getMessage());
        }

        try {
            lt(0.0098, 0.0098);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 0.0098 is less than 0.0098.', $actual->getMessage());
        }

        try {
            lt(0.0098, 0.0099);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 0.0099 is less than 0.0098.', $actual->getMessage());
        }

        try {
            le(0.0098, 0.0099);
            self::fail();
        } catch(ExpectationFailedException $actual) {
            self::assertSame('Failed asserting that 0.0099 is equal to 0.0098 or is less than 0.0098.', $actual->getMessage());
        }
    }
}
