<?php
declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Widget;

use FriendsOfTYPO3\Dashboard\Widgets\AbstractListWidget;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class LatestTasksWidget extends AbstractListWidget
{
    /**
     * @var string
     */
    protected $templateName = 'LatestTasks';

    public function __construct()
    {
        AbstractListWidget::__construct();
        $this->width = 4;
        $this->height = 4;
        $this->title = 'Latest tasks';
        $this->description = 'Shows the latest incoming tasks';
        $this->iconIdentifier = 'dashboard-signin';
    }

    public function prepareData(): void
    {
        /**@var $queryBuilder QueryBuilder**/
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_taskqueue_domain_model_task');
        $statement = $queryBuilder
            ->select('*')
            ->from('tx_taskqueue_domain_model_task')
            ->orderBy('crdate', 'DESC')
            ->setMaxResults(7)
            ->execute();
        $this->items = $statement->fetchAll();
    }

    /**
     * Sets up the Fluid View.
     *
     * @param string $templateName
     */
    protected function initializeView(): void
    {
        parent::initializeView();
        $this->view->setTemplateRootPaths(['EXT:taskqueue/Resources/Private/Backend/Templates/Widgets']);
    }
}
