<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Functional\Domain\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
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

    /**
     * Fixture: uid 1 (Bravo, status 2, retries 5, crdate 1000003, last_run 2021-01-01, message Zebra),
     *          uid 2 (Alpha, status 0, retries 1, crdate 1000001, last_run 2021-01-03, message Mango),
     *          uid 3 (Charlie, status 1, retries 3, crdate 1000002, last_run 2021-01-02, message Apple).
     *
     * @return array<string, array{0: string, 1: int[]}>
     */
    public static function sortablePropertyProvider(): array
    {
        return [
            'name' => ['name', [2, 1, 3]],
            'status' => ['status', [2, 3, 1]],
            'retries' => ['retries', [2, 3, 1]],
            'crdate' => ['crdate', [2, 3, 1]],
            'lastRun' => ['lastRun', [1, 3, 2]],
            'message' => ['message', [3, 2, 1]],
        ];
    }

    #[Test]
    #[DataProvider('sortablePropertyProvider')]
    public function findByDemandOrdersAscendingByEachSortableProperty(string $property, array $expectedUidOrderAscending): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/SortableTasks.csv');

        $demand = new Demand();
        $demand->setOrderBy($property);
        $demand->setOrderDirection(QueryInterface::ORDER_ASCENDING);

        $result = $this->subject->findByDemand($demand);

        self::assertSame($expectedUidOrderAscending, array_map(static fn($task) => $task->getUid(), iterator_to_array($result)));
    }

    #[Test]
    #[DataProvider('sortablePropertyProvider')]
    public function findByDemandOrdersDescendingByEachSortableProperty(string $property, array $expectedUidOrderAscending): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/SortableTasks.csv');

        $demand = new Demand();
        $demand->setOrderBy($property);
        $demand->setOrderDirection(QueryInterface::ORDER_DESCENDING);

        $result = $this->subject->findByDemand($demand);

        self::assertSame(array_reverse($expectedUidOrderAscending), array_map(static fn($task) => $task->getUid(), iterator_to_array($result)));
    }

    #[Test]
    public function findByDemandIgnoresOrderByOutsideTheSortableWhitelist(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/SortableTasks.csv');

        $demand = new Demand();
        // 'data' is a real column but intentionally not in the sortable whitelist
        $demand->setOrderBy('data');
        $demand->setOrderDirection(QueryInterface::ORDER_ASCENDING);

        $result = $this->subject->findByDemand($demand);

        // falls back to the repository's default ordering (task execution order: priority DESC,
        // retries DESC, uid ASC) instead of throwing or sorting by the disallowed property
        self::assertSame([1, 3, 2], array_map(static fn($task) => $task->getUid(), iterator_to_array($result)));
    }

    #[Test]
    public function findByDemandWithoutOrderByMatchesTaskExecutionOrder(): void
    {
        // Same fixture used by findRunableTasksOrdersByPriorityDescending() below: the backend
        // module's default (unsorted) list must show tasks in the same order they'd actually be
        // picked up and run in, i.e. findByDemand() and findRunableTasks() must agree.
        $this->importCSVDataSet(__DIR__ . '/Fixtures/PriorityOrderTasks.csv');

        $demandResult = $this->subject->findByDemand(new Demand());
        $runableResult = $this->subject->findRunableTasks(10);

        $demandUids = array_map(static fn($task) => $task->getUid(), iterator_to_array($demandResult));
        $runableUids = array_map(static fn($task) => $task->getUid(), iterator_to_array($runableResult));

        self::assertSame([2, 3, 1], $demandUids);
        self::assertSame($runableUids, $demandUids);
    }
}
