<?php

namespace Undkonsorten\Taskqueue\Event;

use Symfony\Component\Console\Input\InputInterface;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class BeforeExecuteQueryFindDeferredOutOfIntervalEvent
{
    public function __construct(
        private QueryInterface $query,
    ) {}

    public function getQuery(): QueryInterface
    {
        return $this->query;
    }

    public function setQuery(QueryInterface $query): void
    {
        $this->query = $query;
    }

}
