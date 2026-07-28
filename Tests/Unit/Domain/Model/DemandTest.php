<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\Taskqueue\Domain\Model\Demand;

#[CoversClass(Demand::class)]
final class DemandTest extends UnitTestCase
{
    private Demand $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Demand();
    }

    #[Test]
    public function getStatusInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getStatus());
    }

    #[Test]
    public function setAndGetStatusRoundTrips(): void
    {
        $this->subject->setStatus(2);
        self::assertSame(2, $this->subject->getStatus());
    }

    #[Test]
    public function setStatusToNullIsAllowed(): void
    {
        $this->subject->setStatus(3);
        $this->subject->setStatus(null);
        self::assertNull($this->subject->getStatus());
    }

    #[Test]
    public function getOrderByInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getOrderBy());
    }

    #[Test]
    public function getOrderDirectionInitiallyReturnsAscending(): void
    {
        self::assertSame(QueryInterface::ORDER_ASCENDING, $this->subject->getOrderDirection());
    }

    #[Test]
    public function setAndGetOrderByRoundTrips(): void
    {
        $this->subject->setOrderBy('name');
        self::assertSame('name', $this->subject->getOrderBy());
    }

    #[Test]
    public function setAndGetOrderDirectionRoundTrips(): void
    {
        $this->subject->setOrderDirection(QueryInterface::ORDER_DESCENDING);
        self::assertSame(QueryInterface::ORDER_DESCENDING, $this->subject->getOrderDirection());
    }

    #[Test]
    public function withOrderReturnsNewInstanceWithOrderReplacedAndStatusPreserved(): void
    {
        $this->subject->setStatus(2);
        $this->subject->setOrderBy('name');
        $this->subject->setOrderDirection(QueryInterface::ORDER_ASCENDING);

        $result = $this->subject->withOrder('crdate', QueryInterface::ORDER_DESCENDING);

        self::assertNotSame($this->subject, $result);
        self::assertSame('crdate', $result->getOrderBy());
        self::assertSame(QueryInterface::ORDER_DESCENDING, $result->getOrderDirection());
        self::assertSame(2, $result->getStatus());

        // original is untouched
        self::assertSame('name', $this->subject->getOrderBy());
        self::assertSame(QueryInterface::ORDER_ASCENDING, $this->subject->getOrderDirection());
    }

    #[Test]
    public function getToggledDirectionForReturnsAscendingWhenSwitchingToADifferentColumn(): void
    {
        $this->subject->setOrderBy('name');
        $this->subject->setOrderDirection(QueryInterface::ORDER_DESCENDING);

        self::assertSame(QueryInterface::ORDER_ASCENDING, $this->subject->getToggledDirectionFor('status'));
    }

    #[Test]
    public function getToggledDirectionForReturnsAscendingWhenNoColumnIsActiveYet(): void
    {
        self::assertSame(QueryInterface::ORDER_ASCENDING, $this->subject->getToggledDirectionFor('name'));
    }

    #[Test]
    public function getToggledDirectionForReturnsDescendingWhenColumnIsActiveAndAscending(): void
    {
        $this->subject->setOrderBy('name');
        $this->subject->setOrderDirection(QueryInterface::ORDER_ASCENDING);

        self::assertSame(QueryInterface::ORDER_DESCENDING, $this->subject->getToggledDirectionFor('name'));
    }

    #[Test]
    public function getToggledDirectionForReturnsAscendingWhenColumnIsActiveAndDescending(): void
    {
        $this->subject->setOrderBy('name');
        $this->subject->setOrderDirection(QueryInterface::ORDER_DESCENDING);

        self::assertSame(QueryInterface::ORDER_ASCENDING, $this->subject->getToggledDirectionFor('name'));
    }
}
