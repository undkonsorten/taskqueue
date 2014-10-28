<?php
namespace Undkonsorten\Taskqueue\Controller;


use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
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
class TaskQueueCommandController extends CommandController{
	
	/**
	 * taskRepository
	 *
	 * @var \Undkonsorten\Taskqueue\Domain\Repository\TaskRepository
	 * @inject
	 */
	protected $taskRepository = NULL;
	
	/**
	 * Runs Tasks
	 * @param integer $limit
	 */
	public function runTasksCommand($limit = 10){
		$tasks = $this->taskRepository->findRunableTasks($limit);
		
		foreach ($tasks as $task){
			$task->run();
			$this->taskRepository->update($task);
		}
	}
}

