<?php
/**
 * Copyright (C) php-fn. See LICENSE file for license details.
 */

namespace fn\test\assert;

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
            $this->fail();
        } catch (AssertionFailedError $exception) {
            $this->assertEquals('message', $exception->getMessage());
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
    }

    /**
     */
    public function testNegative(): void
    {
        try {
            equals(1, 2);
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that 2 matches expected 1.', $actual->getMessage());
        }

        try {
            same(1, '1');
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame("Failed asserting that '1' is identical to 1.", $actual->getMessage());
        }

        try {
            true(false);
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that false is true.', $actual->getMessage());
        }

        try {
            false(true);
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that true is false.', $actual->getMessage());
        }

        try {
            not\equals(1, '1');
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame("Failed asserting that '1' is not equal to 1.", $actual->getMessage());
        }

        try {
            not\same(1, 1);
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that 1 is not identical to 1.', $actual->getMessage());
        }

        try {
            not\true(true);
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that true is not true.', $actual->getMessage());
        }

        try {
            not\false(false);
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that false is not false.', $actual->getMessage());
        }

        try {
            type('not-a-type', $this);
            $this->fail();
        } catch (FrameworkException $actual) {
            $this->assertSame(
                'Argument #1 (No Value) of PHPUnit\\Framework\\Assert::assertInstanceOf() must be a class or interface name',
                $actual->getMessage()
            );
        }

        try {
            type('array', null);
            $this->fail();
        } catch (FrameworkException $actual) {
            $this->assertSame('Failed asserting that null is of type "array".', $actual->getMessage());
        }

        try {
            exception(null, static function () {});
            $this->fail();
        } catch (AssertionFailedError $actual) {
            $this->assertSame('Expects exception', $actual->getMessage());
        }

        try {
            exception(new LogicException, static function () {
                throw new RuntimeException('');
            });
            $this->fail();
        } catch (ExpectationFailedException $actual) {
            $this->assertSame(
                'Failed asserting that RuntimeException Object (...) is an instance of class "LogicException".',
                $actual->getMessage()
            );
        }

        try {
            exception('expected', static function () {
                fail('actual');
            });
            $this->fail();
        } catch (ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that two strings are equal.', $actual->getMessage());
        }

        try {
            equals\trial(1, static function () {
                return 2;
            });
            $this->fail();
        } catch (ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that 2 matches expected 1.', $actual->getMessage());
        }

        try {
            equals\trial(new Exception('message'), static function () {
                fail('actual');
            });
        } catch(ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that two strings are equal.', $actual->getMessage());
        }

        try {
            same\trial(1, static function () {
                return '1';
            });
            $this->fail();
        } catch(ExpectationFailedException $actual) {
            $this->assertSame("Failed asserting that '1' is identical to 1.", $actual->getMessage());
        }

        try {
            same\trial(new Exception('message'), static function () {
                fail('actual');
            });
        } catch(ExpectationFailedException $actual) {
            $this->assertSame('Failed asserting that two strings are equal.', $actual->getMessage());
        }
    }
}
