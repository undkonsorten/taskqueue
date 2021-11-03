<?php

namespace Undkonsorten\Taskqueue\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;

class Demand extends AbstractValueObject
{

    /**
     * @var int|null
     */
    protected ?int $status = null;

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

}
