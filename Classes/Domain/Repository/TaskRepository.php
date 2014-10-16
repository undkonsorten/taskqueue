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
	
  // Example for repository wide settings
    public function initializeObject() {
        /** @var $defaultQuerySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
        $defaultQuerySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $defaultQuerySettings->setRespectStoragePage(FALSE);
        $this->setDefaultQuerySettings($defaultQuerySettings);
	}

	public function findRunableTasks($limit){
		$query = $this->createQuery();
		$query->matching(
				$query->logicalOr(
					$query->equals('status',Undkonsorten\Taskqueue\Domain\Model\TaskInterface::WAITING),
					$query->equals('status',Undkonsorten\Taskqueue\Domain\Model\TaskInterface::FAILED)
				)
		);
		$query->setLimit($limit);
		
		$orderings = array(
	  		'priority' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
	 	);
		
		$query->setOrderings($orderings);
		
		return $query->execute();
	}
	
	
}