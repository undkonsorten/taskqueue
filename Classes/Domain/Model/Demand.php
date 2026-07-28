<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class Demand extends AbstractValueObject
{
    /**
     * @var int|null
     */
    protected ?int $status = null;

    protected ?string $orderBy = null;

    protected string $orderDirection = QueryInterface::ORDER_ASCENDING;

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function setOrderBy(?string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    /**
     * Returns a clone of this demand with the sort order replaced, leaving the filter (status) untouched.
     */
    public function withOrder(?string $property, string $direction): self
    {
        $clone = clone $this;
        $clone->orderBy = $property;
        $clone->orderDirection = $direction;
        return $clone;
    }

    /**
     * The direction a column header link should sort by next: toggles away from the current
     * direction when the given property is already the active sort column, otherwise starts
     * ascending.
     */
    public function getToggledDirectionFor(string $property): string
    {
        if ($this->orderBy === $property && $this->orderDirection === QueryInterface::ORDER_ASCENDING) {
            return QueryInterface::ORDER_DESCENDING;
        }
        return QueryInterface::ORDER_ASCENDING;
    }
}
