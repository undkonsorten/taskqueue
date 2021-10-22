<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Eike Starkmann <starkmann@undkonsorten.com>, undkonsorten
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * The repository for Tasks
 */
class TaskRepository extends Repository
{
    protected $defaultOrderings = [
        'tstamp' => QueryInterface::ORDER_DESCENDING,
    ];

    // Example for repository wide settings
    public function initializeObject(): void
    {
        $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $defaultQuerySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($defaultQuerySettings);
    }

    /**
     * Finds all runnabel tasks
     * @param int $limit
     * @param string $whitelist
     * @param string $blacklist
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findRunableTasks($limit = 10, $whitelist = '', $blacklist = ''): QueryResultInterface
    {
        $query = $this->createQuery();
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $whitelist = GeneralUtility::trimExplode(',', $whitelist, true);
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $blacklist = GeneralUtility::trimExplode(',', $blacklist, true);
        $constraints = [
            $query->logicalOr([
                $query->equals('status', TaskInterface::WAITING),
                $query->equals('status', TaskInterface::RETRY),
            ]),
            $query->lessThanOrEqual('startDate', time()),
        ];
        if ($whitelist) {
            $constraints[] = $query->in('name', $whitelist);
        }
        if ($blacklist) {
            $constraints[] = $query->logicalNot(
                $query->in('name', $blacklist)
            );
        }

        $query->matching(
            $query->logicalAnd($constraints)
        );

        $query->setLimit((integer)$limit);

        $orderings = [
            'priority' => QueryInterface::ORDER_DESCENDING,
        ];

        $query->setOrderings($orderings);

        return $query->execute();
    }

    /**
     * Find all finished tasks
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findFinished(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching(
                $query->equals('status', TaskInterface::FINISHED)
        );
        return $query->execute();
    }

    /**
     * Finds all failed tasks
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findFailed(): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->matching(
                $query->equals('status', TaskInterface::FAILED)
        );
        return $query->execute();
    }

    /**
     * @param \DateInterval $dateInterval
     * @return array|QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findOutOfInterval(\DateInterval $dateInterval)
    {
        $now = new \DateTime('now');
        $now->sub($dateInterval);
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                    $query->lessThan('tstamp', $now->getTimestamp()),
                    $query->logicalOr([
                        $query->equals('status', TaskInterface::FAILED),
                        $query->equals('status', TaskInterface::FINISHED)
                    ])
            ])

        );
        return $query->execute();

    }

    /**
     * @param string $wordsInData
     * @return QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByWordsInData(string $wordsInData): QueryResultInterface
    {
        $query = $this->createQuery();
        return $query->matching($query->like('data', sprintf('%%%s%%', $wordsInData)))
            ->execute();

    }

}
