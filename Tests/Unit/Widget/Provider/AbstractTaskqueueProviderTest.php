<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\Widget\Provider;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;
use Undkonsorten\Taskqueue\Widget\Provider\AbstractTaskqueueProvider;
use Undkonsorten\Taskqueue\Widget\Provider\FailedTasksProvider;
use Undkonsorten\Taskqueue\Widget\Provider\FinishedTasksProvider;
use Undkonsorten\Taskqueue\Widget\Provider\IncomingTasksProvider;
use Undkonsorten\Taskqueue\Widget\Provider\TaskThroughputProvider;
use Undkonsorten\Taskqueue\Widget\Provider\WaitingTasksProvider;

#[CoversClass(AbstractTaskqueueProvider::class)]
#[CoversClass(FailedTasksProvider::class)]
#[CoversClass(FinishedTasksProvider::class)]
#[CoversClass(IncomingTasksProvider::class)]
#[CoversClass(TaskThroughputProvider::class)]
#[CoversClass(WaitingTasksProvider::class)]
final class AbstractTaskqueueProviderTest extends UnitTestCase
{
    #[Test]
    public function failedTasksProviderHasCorrectStatus(): void
    {
        $provider = new FailedTasksProvider();
        $status = (new \ReflectionProperty($provider, 'status'))->getValue($provider);
        self::assertSame(TaskInterface::FAILED, $status);
    }

    #[Test]
    public function failedTasksProviderHasCorrectDatefield(): void
    {
        $provider = new FailedTasksProvider();
        $field = (new \ReflectionProperty($provider, 'datefield'))->getValue($provider);
        self::assertSame('crdate', $field);
    }

    #[Test]
    public function finishedTasksProviderHasCorrectStatus(): void
    {
        $provider = new FinishedTasksProvider();
        $status = (new \ReflectionProperty($provider, 'status'))->getValue($provider);
        self::assertSame(TaskInterface::FINISHED, $status);
    }

    #[Test]
    public function finishedTasksProviderHasCorrectDatefield(): void
    {
        $provider = new FinishedTasksProvider();
        $field = (new \ReflectionProperty($provider, 'datefield'))->getValue($provider);
        self::assertSame('crdate', $field);
    }

    #[Test]
    public function waitingTasksProviderHasCorrectStatus(): void
    {
        $provider = new WaitingTasksProvider();
        $status = (new \ReflectionProperty($provider, 'status'))->getValue($provider);
        self::assertSame(TaskInterface::WAITING, $status);
    }

    #[Test]
    public function waitingTasksProviderHasCorrectDatefield(): void
    {
        $provider = new WaitingTasksProvider();
        $field = (new \ReflectionProperty($provider, 'datefield'))->getValue($provider);
        self::assertSame('crdate', $field);
    }

    #[Test]
    public function taskThroughputProviderHasCorrectStatus(): void
    {
        $provider = new TaskThroughputProvider();
        $status = (new \ReflectionProperty($provider, 'status'))->getValue($provider);
        self::assertSame(TaskInterface::FINISHED, $status);
    }

    #[Test]
    public function taskThroughputProviderHasCorrectDatefield(): void
    {
        $provider = new TaskThroughputProvider();
        $field = (new \ReflectionProperty($provider, 'datefield'))->getValue($provider);
        self::assertSame('tstamp', $field);
    }

    #[Test]
    public function incomingTasksProviderHasCorrectDatefield(): void
    {
        $provider = new IncomingTasksProvider();
        $field = (new \ReflectionProperty($provider, 'datefield'))->getValue($provider);
        self::assertSame('crdate', $field);
    }

    #[Test]
    public function getChartDataReturnsExpectedStructure(): void
    {
        // Use a partial mock so DB calls in getNumberOfTasksInPeriod return 0
        $provider = $this->getMockBuilder(FailedTasksProvider::class)
            ->onlyMethods(['getNumberOfTasksInPeriod'])
            ->getMock();
        $provider->method('getNumberOfTasksInPeriod')->willReturn(0);

        $result = $provider->getChartData();

        self::assertArrayHasKey('labels', $result);
        self::assertArrayHasKey('datasets', $result);
        self::assertIsArray($result['labels']);
        self::assertIsArray($result['datasets']);
        self::assertCount(1, $result['datasets']);
        self::assertArrayHasKey('data', $result['datasets'][0]);
        // 31 data points for lastMonth
        self::assertCount(31, $result['labels']);
        self::assertCount(31, $result['datasets'][0]['data']);
    }
}
