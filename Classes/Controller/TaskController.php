<?php
namespace Undkonsorten\Taskqueue\Controller;


use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
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
 * TaskController
 */
class TaskController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * taskRepository
	 *
	 * @var \Undkonsorten\Taskqueue\Domain\Repository\TaskRepository
	 * @inject
	 */
	protected $taskRepository = NULL;

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$tasks = $this->taskRepository->findAll();
		$this->view->assign('tasks', $tasks);
	}

	/**
	 * action show
	 *
	 * @param \Undkonsorten\Taskqueue\Domain\Model\Task $task
	 * @return void
	 */
	public function showAction(\Undkonsorten\Taskqueue\Domain\Model\Task $task) {
		$this->view->assign('task', $task);
	}

	/**
	 * action new
	 *
	 * @param \Undkonsorten\Taskqueue\Domain\Model\TaskInterface $newTask
	 * @ignorevalidation $newTask
	 * @return void
	 */
	public function newAction(\Undkonsorten\Taskqueue\Domain\Model\TaskInterface $newTask = NULL) {
		$this->view->assign('newTask', $newTask);
	}

	/**
	 * action create
	 *
	 * @param \Undkonsorten\Taskqueue\Domain\Model\TaskInterface $newTask
	 * @return void
	 */
	public function createAction(\Undkonsorten\Taskqueue\Domain\Model\TaskInterface $newTask) {
		$this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->taskRepository->add($newTask);
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param \Undkonsorten\Taskqueue\Domain\Model\Task $task
	 * @ignorevalidation $task
	 * @return void
	 */
	public function editAction(\Undkonsorten\Taskqueue\Domain\Model\Task $task) {
		$this->view->assign('task', $task);
	}

	/**
	 * action update
	 *
	 * @param \Undkonsorten\Taskqueue\Domain\Model\Task $task
	 * @return void
	 */
	public function updateAction(\Undkonsorten\Taskqueue\Domain\Model\Task $task) {
		$this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->taskRepository->update($task);
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param \Undkonsorten\Taskqueue\Domain\Model\Task $task
	 * @return void
	 */
	public function deleteAction(\Undkonsorten\Taskqueue\Domain\Model\Task $task) {
		$this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->taskRepository->remove($task);
		$this->redirect('list');
	}
	
	/**
	 * runs an task
	 *
	 * @param \Undkonsorten\Taskqueue\Domain\Model\Task $task
	 * @return void
	 */
	public function runAction(\Undkonsorten\Taskqueue\Domain\Model\Task $task) {
		$task = $this->taskRepository->findByIdentifier($task);
		$task->run();
		$this->addFlashMessage('Task has been executed', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO);
		$this->taskRepository->update($task);
		$this->redirect('list');
	}
	
	/**
	 * Debugs a SQL query from a QueryResult
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult
	 * @param boolean $explainOutput
	 * @return void
	 */
	public function debugQuery(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $queryResult, $explainOutput = FALSE){
		$GLOBALS['TYPO3_DB']->debugOuput = 2;
		if($explainOutput){
			$GLOBALS['TYPO3_DB']->explainOutput = true;
		}
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
		$queryResult->toArray();
		DebuggerUtility::var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
	
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = false;
		$GLOBALS['TYPO3_DB']->explainOutput = false;
		$GLOBALS['TYPO3_DB']->debugOuput = false;
	}

}