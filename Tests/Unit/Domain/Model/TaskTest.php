<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;
use Undkonsorten\Taskqueue\Tests\Unit\Domain\Model\Fixtures\ConcreteTask;

#[CoversClass(Task::class)]
final class TaskTest extends UnitTestCase
{
    private ConcreteTask $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ConcreteTask();
    }

    #[Test]
    public function constructorSetsNameToClassName(): void
    {
        self::assertSame(ConcreteTask::class, $this->subject->getName());
    }

    #[Test]
    public function getShortNameReturnsLastNamespacePart(): void
    {
        self::assertSame('ConcreteTask', $this->subject->getShortName());
    }

    #[Test]
    public function setAndGetNameRoundTrips(): void
    {
        $this->subject->setName('MyCustomTask');
        self::assertSame('MyCustomTask', $this->subject->getName());
    }

    #[Test]
    public function getDataReturnsNullWhenNoDataSet(): void
    {
        self::assertNull($this->subject->getData());
    }

    #[Test]
    public function setAndGetDataRoundTripsArray(): void
    {
        $data = ['key' => 'value', 'number' => 42];
        $this->subject->setData($data);
        self::assertSame($data, $this->subject->getData());
    }

    #[Test]
    public function setDataEncodesAsJson(): void
    {
        $this->subject->setData(['foo' => 'bar']);
        self::assertSame(['foo' => 'bar'], $this->subject->getData());
    }

    #[Test]
    public function getStatusInitiallyReturnsZero(): void
    {
        self::assertSame(0, $this->subject->getStatus());
    }

    #[Test]
    public function setAndGetStatusRoundTrips(): void
    {
        $this->subject->setStatus(TaskInterface::RUNNING);
        self::assertSame(TaskInterface::RUNNING, $this->subject->getStatus());
    }

    #[Test]
    public function getStartDateInitiallyReturnsZero(): void
    {
        self::assertSame(0, $this->subject->getStartDate());
    }

    #[Test]
    public function setAndGetStartDateRoundTrips(): void
    {
        $this->subject->setStartDate(1_700_000_000);
        self::assertSame(1_700_000_000, $this->subject->getStartDate());
    }

    #[Test]
    public function getMessageInitiallyReturnsEmptyString(): void
    {
        self::assertSame('', $this->subject->getMessage());
    }

    #[Test]
    public function setAndGetMessageRoundTrips(): void
    {
        $this->subject->setMessage('Something failed');
        self::assertSame('Something failed', $this->subject->getMessage());
    }

    #[Test]
    public function getPriorityInitiallyReturnsZero(): void
    {
        self::assertSame(0, $this->subject->getPriority());
    }

    #[Test]
    public function setAndGetPriorityRoundTrips(): void
    {
        $this->subject->setPriority(5);
        self::assertSame(5, $this->subject->getPriority());
    }

    #[Test]
    public function getRetriesInitiallyReturnsThree(): void
    {
        self::assertSame(3, $this->subject->getRetries());
    }

    #[Test]
    public function setAndGetRetriesRoundTrips(): void
    {
        $this->subject->setRetries(0);
        self::assertSame(0, $this->subject->getRetries());
    }

    #[Test]
    public function getTtlInitiallyReturns900(): void
    {
        self::assertSame(900, $this->subject->getTtl());
    }

    #[Test]
    public function setAndGetTtlRoundTrips(): void
    {
        $this->subject->setTtl(300);
        self::assertSame(300, $this->subject->getTtl());
    }

    #[Test]
    public function getLastRunInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getLastRun());
    }

    #[Test]
    public function setAndGetLastRunRoundTrips(): void
    {
        $dt = new \DateTime('2024-01-01 12:00:00');
        $this->subject->setLastRun($dt);
        self::assertSame($dt, $this->subject->getLastRun());
    }

    #[Test]
    public function setLastRunAcceptsNull(): void
    {
        $this->subject->setLastRun(new \DateTime());
        $this->subject->setLastRun(null);
        self::assertNull($this->subject->getLastRun());
    }

    #[Test]
    public function markRunningSetsStatusAndLastRun(): void
    {
        $before = new \DateTime();
        $this->subject->markRunning();
        self::assertSame(TaskInterface::RUNNING, $this->subject->getStatus());
        self::assertInstanceOf(\DateTime::class, $this->subject->getLastRun());
        self::assertGreaterThanOrEqual($before->getTimestamp(), $this->subject->getLastRun()->getTimestamp());
    }

    #[Test]
    public function markFinishedSetsFinishedStatus(): void
    {
        $this->subject->markFinished();
        self::assertSame(TaskInterface::FINISHED, $this->subject->getStatus());
    }

    #[Test]
    public function markFailedSetsFailedStatus(): void
    {
        $this->subject->markFailed();
        self::assertSame(TaskInterface::FAILED, $this->subject->getStatus());
    }

    #[Test]
    public function markRetrySetsRetryStatus(): void
    {
        $this->subject->markRetry();
        self::assertSame(TaskInterface::RETRY, $this->subject->getStatus());
    }

    #[Test]
    public function reactivateSetsRetryStatusAndRetries(): void
    {
        $this->subject->markFailed();
        $this->subject->reactivate(5);
        self::assertSame(TaskInterface::RETRY, $this->subject->getStatus());
        self::assertSame(5, $this->subject->getRetries());
    }

    #[Test]
    public function reactivateUsesDefaultRetriesOfThree(): void
    {
        $this->subject->setRetries(0);
        $this->subject->reactivate();
        self::assertSame(3, $this->subject->getRetries());
    }

    #[Test]
    public function getAdditionalInformationReturnsEmptyString(): void
    {
        self::assertSame('', $this->subject->getAdditionalInformation());
    }

    #[Test]
    public function getAdditionalDataReturnsEmptyArray(): void
    {
        self::assertSame([], $this->subject->getAdditionalData());
    }

    #[Test]
    public function getAllDataMergesDataAndAdditionalData(): void
    {
        $this->subject->setData(['from_data' => 1]);
        self::assertSame(['from_data' => 1], $this->subject->getAllData());
    }

    #[Test]
    public function getRecordReturnsNull(): void
    {
        self::assertNull($this->subject->getRecord());
    }

    #[Test]
    public function getRecordLabelReturnsNull(): void
    {
        self::assertNull($this->subject->getRecordLabel());
    }

    #[Test]
    public function jsonSerializeReturnsExpectedKeys(): void
    {
        $serialized = $this->subject->jsonSerialize();
        self::assertArrayHasKey('name', $serialized);
        self::assertArrayHasKey('status', $serialized);
        self::assertArrayHasKey('startDate', $serialized);
        self::assertArrayHasKey('lastRun', $serialized);
        self::assertArrayHasKey('message', $serialized);
        self::assertArrayHasKey('priority', $serialized);
        self::assertArrayHasKey('retries', $serialized);
        self::assertArrayHasKey('ttl', $serialized);
    }

    #[Test]
    public function setPropertyStoresScalarInData(): void
    {
        // Use reflection to call the protected setProperty / getProperty
        $set = new \ReflectionMethod($this->subject, 'setProperty');
        $set->setAccessible(true);
        $get = new \ReflectionMethod($this->subject, 'getProperty');
        $get->setAccessible(true);

        $set->invoke($this->subject, 'myKey', 'myValue');
        self::assertSame('myValue', $get->invoke($this->subject, 'myKey'));
    }

    #[Test]
    public function setPropertyStoresArrayInData(): void
    {
        $set = new \ReflectionMethod($this->subject, 'setProperty');
        $set->setAccessible(true);
        $get = new \ReflectionMethod($this->subject, 'getProperty');
        $get->setAccessible(true);

        $set->invoke($this->subject, 'list', [1, 2, 3]);
        self::assertSame([1, 2, 3], $get->invoke($this->subject, 'list'));
    }

    #[Test]
    public function setPropertyStoresNullInData(): void
    {
        $set = new \ReflectionMethod($this->subject, 'setProperty');
        $set->setAccessible(true);
        $get = new \ReflectionMethod($this->subject, 'getProperty');
        $get->setAccessible(true);

        $set->invoke($this->subject, 'nullable', null);
        self::assertNull($get->invoke($this->subject, 'nullable'));
    }

    #[Test]
    public function setPropertyThrowsForObjectValue(): void
    {
        $set = new \ReflectionMethod($this->subject, 'setProperty');
        $set->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1452100146);
        $set->invoke($this->subject, 'obj', new \stdClass());
    }

    #[Test]
    public function setPropertyThrowsForArrayContainingObject(): void
    {
        $set = new \ReflectionMethod($this->subject, 'setProperty');
        $set->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1452100147);
        $set->invoke($this->subject, 'arr', [new \stdClass()]);
    }

    #[Test]
    public function getPropertyReturnsNullForMissingKey(): void
    {
        $get = new \ReflectionMethod($this->subject, 'getProperty');
        $get->setAccessible(true);
        self::assertNull($get->invoke($this->subject, 'nonexistent'));
    }
}
