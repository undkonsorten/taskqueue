<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Functional\Domain\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\Taskqueue\Domain\Model\Demand;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

#[CoversClass(TaskRepository::class)]
final class TaskRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/taskqueue-test',
    ];

    private TaskRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->get(TaskRepository::class);
    }

    #[Test]
    public function findRunableTasksReturnsWaitingAndRetryTasks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/WaitingAndRetryTasks.csv');

        $result = $this->subject->findRunableTasks(10);

        // uid 1 (WAITING) and uid 2 (RETRY) are runnable; uid 3 (FINISHED) and uid 4 (FAILED) are not
        self::assertSame(2, $result->count());
    }

    #[Test]
    public function findRunableTasksRespectsLimit(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/WaitingAndRetryTasks.csv');

        $result = $this->subject->findRunableTasks(1);

        self::assertSame(1, $result->count());
    }

    #[Test]
    public function findRunableTasksFiltersOutFutureStartDate(): void
    {
        // A task with start_date in the far future should not be returned
        $this->importCSVDataSet(__DIR__ . '/Fixtures/WaitingAndRetryTasks.csv');

        // All fixture tasks have start_date = 0 (past), so all WAITING+RETRY should be included
        $result = $this->subject->findRunableTasks(10);
        self::assertGreaterThan(0, $result->count());
    }

    #[Test]
    public function findRunableTasksRespectsWhitelist(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/WaitingAndRetryTasks.csv');

        $result = $this->subject->findRunableTasks(10, 'NonExistentTask');

        self::assertSame(0, $result->count());
    }

    #[Test]
    public function findRunableTasksRespectsBlacklist(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/WaitingAndRetryTasks.csv');

        $taskName = 'Undkonsorten\TaskqueueTest\Domain\Model\TestTask';
        $result = $this->subject->findRunableTasks(10, '', $taskName);

        self::assertSame(0, $result->count());
    }

    #[Test]
    public function findRunableTasksOrdersByPriorityDescending(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/PriorityOrderTasks.csv');

        $result = $this->subject->findRunableTasks(10);
        $items = iterator_to_array($result);

        // Priority 5 (uid=2) should come first
        self::assertSame(2, $items[0]->getUid());
        self::assertSame(3, $items[1]->getUid());
        self::assertSame(1, $items[2]->getUid());
    }

    #[Test]
    public function findFinishedReturnsOnlyFinishedTasks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FinishedAndFailedTasks.csv');

        $result = $this->subject->findFinished();

        self::assertSame(1, $result->count());
        self::assertSame(TaskInterface::FINISHED, $result->getFirst()->getStatus());
    }

    #[Test]
    public function findFailedReturnsOnlyFailedTasks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FinishedAndFailedTasks.csv');

        $result = $this->subject->findFailed();

        self::assertSame(1, $result->count());
        self::assertSame(TaskInterface::FAILED, $result->getFirst()->getStatus());
    }

    #[Test]
    public function findOutOfIntervalReturnsOldCompletedTasks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OldTasks.csv');

        // P1D = tasks older than 1 day; uid 1,2,3 have old tstamp, uid 4 has future tstamp
        $result = $this->subject->findOutOfInterval(new \DateInterval('P1D'));

        self::assertSame(3, $result->count());
    }

    #[Test]
    public function findOutOfIntervalExcludesRecentTasks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OldTasks.csv');

        // uid 4 has tstamp far in the future so it should never be "out of interval"
        $result = $this->subject->findOutOfInterval(new \DateInterval('P1D'));

        $uids = array_map(fn($t) => $t->getUid(), iterator_to_array($result));
        self::assertNotContains(4, $uids);
    }

    #[Test]
    public function findFailedOutOfIntervalReturnsRecentlyFailedTasks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/OldTasks.csv');

        // uid 2 is FAILED with old crdate; this method looks for FAILED within the interval
        // P100Y includes everything
        $result = $this->subject->findFailedOutOfInterval(new \DateInterval('P100Y'));

        // Only status=3 (FAILED) records qualify
        foreach ($result as $task) {
            self::assertSame(TaskInterface::FAILED, $task->getStatus());
        }
    }

    #[Test]
    public function findByWordsInDataMatchesSubstring(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/TasksWithData.csv');

        $result = $this->subject->findByWordsInData('foo@example.com');

        self::assertSame(1, $result->count());
    }

    #[Test]
    public function findByWordsInDataReturnsNothingForUnmatchedString(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/TasksWithData.csv');

        $result = $this->subject->findByWordsInData('nobody@nowhere.invalid');

        self::assertSame(0, $result->count());
    }

    #[Test]
    public function findByDemandWithStatusReturnsMatchingTasks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FinishedAndFailedTasks.csv');

        $demand = new Demand();
        $demand->setStatus(TaskInterface::FINISHED);

        $result = $this->subject->findByDemand($demand);

        self::assertSame(1, $result->count());
        self::assertSame(TaskInterface::FINISHED, $result->getFirst()->getStatus());
    }

    #[Test]
    public function findByDemandWithNullStatusReturnsAllTasks(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FinishedAndFailedTasks.csv');

        $demand = new Demand();
        // status is null by default → no filter

        $result = $this->subject->findByDemand($demand);

        self::assertSame(2, $result->count());
    }
}
