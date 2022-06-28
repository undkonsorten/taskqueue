<?php
declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Widget\Provider;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class IncomingTasksProvider extends AbstractTaskqueueProvider
{
    /**
     * @var string
     */
    protected $title = 'Tasks incoming';

    protected $datefield = 'crdate';

    protected function getNumberOfTasksInPeriod(int $start, int $end): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_taskqueue_domain_model_task');
        return (int)$queryBuilder
            ->select('*')
            ->from('tx_taskqueue_domain_model_task')
            ->where(
                $queryBuilder->expr()->gte($this->datefield, $start),
                $queryBuilder->expr()->lte($this->datefield, $end)
            )
            ->execute()
            ->rowCount();
    }

}
