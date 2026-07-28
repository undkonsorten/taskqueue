<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\Taskqueue\Controller\TaskController;
use Undkonsorten\Taskqueue\Domain\Model\Demand;

#[CoversClass(TaskController::class)]
final class TaskControllerTest extends UnitTestCase
{
    private TaskController $subject;

    protected function setUp(): void
    {
        parent::setUp();
        // buildSortableColumns() is a pure function of a Demand and touches none of the
        // controller's collaborators (repository, module template, icons, ...), some of which
        // (e.g. ModuleTemplateFactory) are final and can't be doubled - so the constructor is
        // skipped entirely rather than stubbing dependencies that are never used here.
        $this->subject = (new \ReflectionClass(TaskController::class))->newInstanceWithoutConstructor();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSortableColumns(Demand $demand): array
    {
        $method = new \ReflectionMethod($this->subject, 'buildSortableColumns');
        return $method->invoke($this->subject, $demand);
    }

    #[Test]
    public function returnsAllSortableColumnsInOrder(): void
    {
        $columns = $this->buildSortableColumns(new Demand());

        self::assertSame(
            ['status', 'name', 'retries', 'crdate', 'lastRun', 'message'],
            array_keys($columns),
        );
    }

    #[Test]
    public function noColumnIsActiveWhenDemandHasNoOrderBy(): void
    {
        $columns = $this->buildSortableColumns(new Demand());

        foreach ($columns as $column) {
            self::assertFalse($column['active']);
            self::assertNull($column['direction']);
        }
    }

    #[Test]
    public function theCurrentlySortedColumnIsMarkedActiveWithItsDirection(): void
    {
        $demand = new Demand();
        $demand->setOrderBy('name');
        $demand->setOrderDirection(QueryInterface::ORDER_DESCENDING);

        $columns = $this->buildSortableColumns($demand);

        self::assertTrue($columns['name']['active']);
        self::assertSame(QueryInterface::ORDER_DESCENDING, $columns['name']['direction']);

        self::assertFalse($columns['status']['active']);
        self::assertNull($columns['status']['direction']);
    }

    #[Test]
    public function targetDemandOfTheActiveColumnTogglesDirection(): void
    {
        $demand = new Demand();
        $demand->setOrderBy('name');
        $demand->setOrderDirection(QueryInterface::ORDER_ASCENDING);

        $columns = $this->buildSortableColumns($demand);

        self::assertSame('name', $columns['name']['targetDemand']->getOrderBy());
        self::assertSame(QueryInterface::ORDER_DESCENDING, $columns['name']['targetDemand']->getOrderDirection());
    }

    #[Test]
    public function targetDemandOfInactiveColumnsStartsAscending(): void
    {
        $demand = new Demand();
        $demand->setOrderBy('name');
        $demand->setOrderDirection(QueryInterface::ORDER_DESCENDING);

        $columns = $this->buildSortableColumns($demand);

        self::assertSame('status', $columns['status']['targetDemand']->getOrderBy());
        self::assertSame(QueryInterface::ORDER_ASCENDING, $columns['status']['targetDemand']->getOrderDirection());
    }

    #[Test]
    public function targetDemandAlwaysPreservesTheCurrentStatusFilter(): void
    {
        $demand = new Demand();
        $demand->setStatus(2);
        $demand->setOrderBy('name');

        $columns = $this->buildSortableColumns($demand);

        foreach ($columns as $column) {
            self::assertSame(2, $column['targetDemand']->getStatus());
        }
    }
}
