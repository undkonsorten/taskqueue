<?php
namespace Undkonsorten\Taskqueue\Domain\Repository;


use Undkonsorten;
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
class TaskRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	protected $defaultOrderings = [
		'startDate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
	];

  // Example for repository wide settings
    public function initializeObject() {
        /** @var $defaultQuerySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $defaultQuerySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $defaultQuerySettings->setRespectStoragePage(FALSE);
        $this->setDefaultQuerySettings($defaultQuerySettings);
	}

	/**
	 * Finds all runnabel tasks
	 * @param integer $limit
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findRunableTasks($limit = 10){
		$query = $this->createQuery();
		$query->matching(
			$query->logicalAnd(
                $query->equals('status',Undkonsorten\Taskqueue\Domain\Model\TaskInterface::WAITING),
				$query->lessThanOrEqual('startDate', time())
			)

		);

		$query->setLimit($limit);

		$orderings = array(
	  		'priority' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
	 	);

		$query->setOrderings($orderings);

		return $query->execute();
	}

	/**
	 * Find all finished tasks
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findFinished(){
		$query = $this->createQuery();
		$query->matching(
				$query->equals('status',Undkonsorten\Taskqueue\Domain\Model\TaskInterface::FINISHED)
		);
		return $query->execute();
	}

	/**
	 * Finds all failed tasks
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findFailed(){
		$query = $this->createQuery();
		$query->matching(
				$query->equals('status',Undkonsorten\Taskqueue\Domain\Model\TaskInterface::FAILED)
		);
		return $query->execute();
	}

}
