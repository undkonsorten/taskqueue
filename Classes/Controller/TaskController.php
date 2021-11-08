<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Undkonsorten\Taskqueue\Domain\Model\Demand;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;
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

    /**
     * @var ModuleTemplateFactory
     */
    protected $moduleTemplateFactory;

    /**
     * @var ModuleTemplate
     */
    protected $moduleTemplate;

    /** @var IconFactory */
    protected $iconFactory;

    public function __construct(
        TaskRepository $taskRepository,
        PersistenceManagerInterface $persistenceManager,
        ModuleTemplateFactory $moduleTemplateFactory,
        IconFactory $iconFactory
    )
    {
        $this->taskRepository = $taskRepository;
        $this->persitenceManager = $persistenceManager;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->iconFactory = $iconFactory;
    }

    public function initializeAction()
    {
        parent::initializeAction(); // TODO: Change the autogenerated stub
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $menu = $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('WebFuncJumpMenu');
        $menuItem = $menu
            ->makeMenuItem()
            ->setHref(
                $this->uriBuilder->buildBackendUri()
            )
            ->setTitle('Task');
        $menu->addMenuItem($menuItem);
        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        $this->buildButtons();

        if ($this->arguments->hasArgument('demand')) {
            $propertyMappingConfiguration = $this->arguments['demand']->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowCreationForSubProperty('status');
            $propertyMappingConfiguration->allowProperties('status');
            $propertyMappingConfiguration->setTypeConverterOption(PersistentObjectConverter::class, PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, TRUE);
        }

    }


    /**
     * @param int $currentPage
     * @param Demand|null $demand
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function listAction(int $currentPage = 1, Demand $demand = null): ResponseInterface
    {
        if(is_null($demand)){
            $tasks = $this->taskRepository->findAll();
        }else{
            $tasks = $this->taskRepository->findByDemand($demand);
        }
        $status = [
            TaskInterface::FINISHED => LocalizationUtility::translate('tx_taskqueue_domain_model_task.status.finished','taskqueue'),
            TaskInterface::FAILED => LocalizationUtility::translate('tx_taskqueue_domain_model_task.status.failed','taskqueue'),
            TaskInterface::WAITING => LocalizationUtility::translate('tx_taskqueue_domain_model_task.status.waiting','taskqueue'),
            TaskInterface::RUNNING => LocalizationUtility::translate('tx_taskqueue_domain_model_task.status.running','taskqueue'),
            TaskInterface::RETRY => LocalizationUtility::translate('tx_taskqueue_domain_model_task.status.retry','taskqueue'),
        ];
        $currentPage = $this->request->hasArgument('currentPage') ? $this->request->getArgument('currentPage') : $currentPage;
        $paginator = new QueryResultPaginator($tasks, (integer)$currentPage, (integer)$this->settings['pagination']['itemsPerPage']);
        $simplePagination = new SimplePagination($paginator);
        $pagination = $this->buildSimplePagination($simplePagination, $paginator);
        $this->view->assign('tasks', $paginator->getPaginatedItems());
        $this->view->assign('pagination', $pagination);
        $this->view->assign('paginator', $paginator);
        $this->view->assign('demand', $demand);
        $this->view->assign('status', $status);
        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * action show
     *
     * @param Task $task
     */
    public function showAction(Task $task): ResponseInterface
    {
        $this->view->assign('task', $task);
        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    public function searchAction(): ResponseInterface
    {
        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
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
        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
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

    /**
     * build simple pagination
     *
     * @param SimplePagination $simplePagination
     * @param QueryResultPaginator $paginator
     * @return array
     */
    protected function buildSimplePagination(SimplePagination $simplePagination, QueryResultPaginator $paginator)
    {
        $firstPage = $simplePagination->getFirstPageNumber();
        $lastPage = $simplePagination->getLastPageNumber();
        return [
            'lastPageNumber' => $lastPage,
            'firstPageNumber' => $firstPage,
            'nextPageNumber' => $simplePagination->getNextPageNumber(),
            'previousPageNumber' => $simplePagination->getPreviousPageNumber(),
            'startRecordNumber' => $simplePagination->getStartRecordNumber(),
            'endRecordNumber' => $simplePagination->getEndRecordNumber(),
            'currentPageNumber' => $paginator->getCurrentPageNumber(),
            'pages' => range($firstPage, $lastPage)
        ];
    }

    protected function buildButtons()
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $buttons = [
            [
                'table' => 'tx_taskqueue_domain_model_task',
                'label' => 'module.list',
                'action' => 'list',
                'icon' => 'actions-list'
            ],
            [
                'table' => 'tx_taskqueue_domain_model_task',
                'label' => 'module.search',
                'action' => 'search',
                'icon' => 'actions-search'
            ],
        ];
        foreach ($buttons as $key => $tableConfiguration) {
            $title = LocalizationUtility::translate($tableConfiguration['label'],'taskqueue');
            $viewButton = $buttonBar->makeLinkButton()
                ->setHref($this->uriBuilder->reset()->setRequest($this->request)->uriFor(
                    $tableConfiguration['action'],
                    [],
                    'Task'
                ))
                ->setDataAttributes([
                    'toggle' => 'tooltip',
                    'placement' => 'bottom',
                    'title' => $title])
                ->setTitle($title)
                ->setIcon($this->iconFactory->getIcon($tableConfiguration['icon'], Icon::SIZE_SMALL));
            $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
        }

        $deleteFinished = $buttonBar->makeLinkButton()
            ->setShowLabelText(true)
            ->setHref($this->uriBuilder->reset()->setRequest($this->request)->uriFor(
                'deleteFinished',
                [
                ],
                'Task'
            ))
            ->setDataAttributes([
                'toggle' => 'tooltip',
                'placement' => 'bottom',
                'title' => $title])
            ->setTitle(LocalizationUtility::translate('button.deleteFinished','taskqueue'))
            ->setIcon($this->iconFactory->getIcon('actions-delete', Icon::SIZE_SMALL,'tx-taskqueue-status-finished'));
        $buttonBar->addButton($deleteFinished, ButtonBar::BUTTON_POSITION_LEFT, 3);

        $deleteFailed = $buttonBar->makeLinkButton()
            ->setShowLabelText(true)
            ->setHref($this->uriBuilder->reset()->setRequest($this->request)->uriFor(
                'deleteFailed',
                [
                ],
                'Task'
            ))
            ->setDataAttributes([
                'toggle' => 'tooltip',
                'placement' => 'bottom',
                'title' => $title])
            ->setTitle(LocalizationUtility::translate('button.deleteFailed','taskqueue'))
            ->setIcon($this->iconFactory->getIcon('actions-delete', Icon::SIZE_SMALL,'tx-taskqueue-status-failed'));
        $buttonBar->addButton($deleteFailed, ButtonBar::BUTTON_POSITION_LEFT, 3);

        // Refresh
        $refreshButton = $buttonBar->makeLinkButton()
            ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($refreshButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }




}
