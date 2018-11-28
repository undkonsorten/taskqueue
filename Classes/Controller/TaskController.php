<?php
namespace Undkonsorten\Taskqueue\Controller;

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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

/**
 * TaskController
 */
class TaskController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * taskRepository
	 *
	 * @var TaskRepository
	 */
	protected $taskRepository = NULL;
    /**
     * @var PersistenceManagerInterface
     */
    protected $persitenceManager;

    public function injectTaskrepository(TaskRepository $taskRepository){
        $this->taskRepository = $taskRepository;
    }

    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager){
        $this->persitenceManager = $persistenceManager;
    }

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
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
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
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
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
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
	public function deleteAction(\Undkonsorten\Taskqueue\Domain\Model\Task $task) {
		$this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->taskRepository->remove($task);
		$this->redirect('list');
	}

    /**
     * action delete failed tasks
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
	public function deleteFailedAction() {
		$tasks = $this->taskRepository->findFailed();
        $this->addFlashMessageForDeletion($tasks);
		foreach($tasks as $task) {
			$this->taskRepository->remove($task);
		}
		$this->redirect('list');
	}

    /**
     * action delete finished tasks
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
	public function deleteFinishedAction() {
		$tasks = $this->taskRepository->findFinished();
        $this->addFlashMessageForDeletion($tasks);
		foreach($tasks as $task) {
			$this->taskRepository->remove($task);
		}
		$this->redirect('list');
	}

    protected function addFlashMessageForDeletion(QueryResultInterface $tasks)
    {
        $title = 'Delete tasks';
        if ($tasks->count() === 0) {
            $this->addFlashMessage('Nothing to delete', $title, FlashMessage::INFO);
        } else {
            $this->addFlashMessage(sprintf('%d task(s) deleted.', $tasks->count()), $title, FlashMessage::OK);
        }
    }

    /**
     * runs an task
     *
     * @param \Undkonsorten\Taskqueue\Domain\Model\Task $task
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
	public function runAction(\Undkonsorten\Taskqueue\Domain\Model\Task $task) {
		$task = $this->taskRepository->findByIdentifier($task->getUid());
        try{
            if($task->getRetries() !== 0) {
                $task->setRetries($task->getRetries() - 1);
            }
            /**@var \Undkonsorten\Taskqueue\Domain\Model\Task $task **/
            $task->run();
            $task->setStatus(TaskInterface::FINISHED);
        }catch(\Exception $exception){
            $task->setMessage($exception->getMessage());
            if($task->getRetries() === 0){
                $task->setStatus(TaskInterface::FAILED);
            }else{
                $task->setStatus(TaskInterface::RETRY);
            }
            $this->taskRepository->update($task);
            $this->persitenceManager->persistAll();
            throw $exception;
        }

		$this->addFlashMessage('Task has been executed', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO);
		$this->taskRepository->update($task);
		$this->redirect('list');
	}

}
