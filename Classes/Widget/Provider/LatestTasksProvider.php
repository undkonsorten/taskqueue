<?php
declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Widget\Provider;

use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;


class LatestTasksProvider implements ListDataProviderInterface
{

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @throws Exception
     */
    public function getItems(): array
    {
        /**@var $queryBuilder QueryBuilder**/
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_taskqueue_domain_model_task');
        return $queryBuilder
            ->select('*')
            ->from('tx_taskqueue_domain_model_task')
            ->orderBy('crdate', 'DESC')->setMaxResults(7)->executeQuery()
            ->fetchAllAssociative();
    }

}
