<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
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

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

/**
 * TaskController
 */
class TaskController extends ActionController
{

    /**
     * taskRepository
     *
     * @var TaskRepository
     */
    protected $taskRepository;
    /**
     * @var PersistenceManagerInterface
     */
    protected $persitenceManager;

    public function injectTaskrepository(TaskRepository $taskRepository): void
    {
        $this->taskRepository = $taskRepository;
    }

    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager): void
    {
        $this->persitenceManager = $persistenceManager;
    }

    /**
     * action list
     */
    public function listAction(): ResponseInterface
    {
        $tasks = $this->taskRepository->findAll();
        $this->view->assign('tasks', $tasks);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param Task $task
     */
    public function showAction(Task $task): ResponseInterface
    {
        $this->view->assign('task', $task);
        return $this->htmlResponse();
    }

    public function searchAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * @param int $uid
     * @throws StopActionException
     */
    public function searchUidAction(?int $uid = null): ResponseInterface
    {
        $task = $this->taskRepository->findByUid($uid);
        if (!$task) {
            $this->addFlashMessage(sprintf('Task with uid %d could not be found.', $uid), 'Task not found', FlashMessage::WARNING);
            $this->forwardToReferringRequest();
        }
        return (new ForwardResponse('show'))->withArguments(['task' => $task]);
        return $this->htmlResponse();
    }

    /**
     * @param string $wordsInData
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function searchResultAction(string $wordsInData = ''): ResponseInterface
    {
        $tasks = $this->taskRepository->findByWordsInData($wordsInData);
        $this->view->assign('tasks', $tasks);
        $this->view->assign('searchWords', $wordsInData);
        return $this->htmlResponse();
    }

    /**
     * action delete
     *
     * @param Task $task
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     */
    public function deleteAction(Task $task): void
    {
        $this->addFlashMessage(sprintf('%s [%d] was deleted.', $task->getShortName(), $task->getUid()), 'Task deleted', AbstractMessage::OK);
        $this->taskRepository->remove($task);
        $this->redirect('list');
    }

    /**
     * @param Task $task
     * @param int $retries
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function reactivateAction(Task $task, int $retries = 3)
    {
        $this->addFlashMessage(sprintf('%s [%d] was reactivated with %d retries.', $task->getShortName(), $task->getUid(), $retries), 'Task reactivated', AbstractMessage::OK);
        $task->reactivate($retries);
        $this->taskRepository->update($task);
        $this->redirect('list');
    }

    /**
     * action delete failed tasks
     *
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     */
    public function deleteFailedAction(): void
    {
        $tasks = $this->taskRepository->findFailed();
        $this->addFlashMessageForDeletion($tasks);
        foreach ($tasks as $task) {
            $this->taskRepository->remove($task);
        }
        $this->redirect('list');
    }

    /**
     * action delete finished tasks
     *
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     */
    public function deleteFinishedAction(): void
    {
        $tasks = $this->taskRepository->findFinished();
        $this->addFlashMessageForDeletion($tasks);
        foreach ($tasks as $task) {
            $this->taskRepository->remove($task);
        }
        $this->redirect('list');
    }

    protected function addFlashMessageForDeletion(QueryResultInterface $tasks): void
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
     * @param Task $task
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \Exception
     */
    public function runAction(Task $task): void
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        /* @TODO this code duplicates in \Undkonsorten\Taskqueue\Controller\RunTasksCommand, maybe move to service class */
        /** @var Task $task */
        $task = $this->taskRepository->findByIdentifier($task->getUid());
        try {
            if ($task->getRetries() !== 0) {
                $task->setRetries($task->getRetries() - 1);
            }
            $task->markRunning();
            $this->taskRepository->update($task);
            $this->persitenceManager->persistAll();
            $task->run();
            $task->markFinished();
        } catch (\Exception $exception) {
            $task->setMessage($exception->getMessage());
            if ($task->getRetries() === 0) {
                $task->markFailed();
            } else {
                $task->markRetry();
            }
            $this->taskRepository->update($task);
            $this->persitenceManager->persistAll();
            throw $exception;
        }

        $this->addFlashMessage('Task has been executed', '', AbstractMessage::INFO);
        $this->taskRepository->update($task);
        $this->redirect('list');
    }
}
