<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\Taskqueue\Exception\StopRunException;

#[CoversClass(StopRunException::class)]
final class StopRunExceptionTest extends UnitTestCase
{
    #[Test]
    public function getTasknameReturnsConstructorArgument(): void
    {
        $exception = new StopRunException('MyTask', 'Something stopped');

        self::assertSame('MyTask', $exception->getTaskname());
    }

    #[Test]
    public function getMessageReturnsConstructorArgument(): void
    {
        $exception = new StopRunException('MyTask', 'Something stopped', 42);

        self::assertSame('Something stopped', $exception->getMessage());
    }

    #[Test]
    public function getCodeReturnsConstructorArgument(): void
    {
        $exception = new StopRunException('MyTask', 'msg', 99);

        self::assertSame(99, $exception->getCode());
    }

    #[Test]
    public function setTasknameOverridesTaskname(): void
    {
        $exception = new StopRunException('OldTask');
        $exception->setTaskname('NewTask');

        self::assertSame('NewTask', $exception->getTaskname());
    }

    #[Test]
    public function isThrowable(): void
    {
        $exception = new StopRunException('T');

        $caught = null;
        try {
            throw $exception;
        } catch (StopRunException $e) {
            $caught = $e;
        }
        self::assertSame($exception, $caught);
    }
}
