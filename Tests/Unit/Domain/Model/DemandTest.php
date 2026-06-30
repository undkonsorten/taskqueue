<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
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
}
