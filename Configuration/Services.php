<?php
declare(strict_types=1);

namespace GeorgRinger\Doc;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Dashboard\Dashboard;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;
use TYPO3\CMS\Dashboard\Widgets\CtaWidget;
use Undkonsorten\Taskqueue\Widget\LatestTasksWidget;
use Undkonsorten\Taskqueue\Widget\Provider\FailedTasksProvider;
use Undkonsorten\Taskqueue\Widget\Provider\FinishedTasksProvider;
use Undkonsorten\Taskqueue\Widget\Provider\IncomingTasksProvider;
use Undkonsorten\Taskqueue\Widget\Provider\LatestTasksProvider;
use Undkonsorten\Taskqueue\Widget\Provider\TaskThroughputProvider;
use Undkonsorten\Taskqueue\Widget\Provider\WaitingTasksProvider;

return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder) {
    $services = $configurator->services();

    if ($containerBuilder->hasDefinition(Dashboard::class)) {
        $services->set('dashboard.widget.taskqueue.latestTasks')
            ->class(LatestTasksWidget::class)
            ->arg('$dataProvider', new Reference(LatestTasksProvider::class))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', null)
            ->arg('$options', ['refreshAvailable' => true])
            ->tag('dashboard.widget', [
                'identifier' => 'latesttasks',
                'groupNames' => 'systemInfo',
                'title' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.latesttasks.title',
                'description' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.latesttasks.description',
                'iconIdentifier' => 'content-widget-text',
                'height' => 'medium',
                'width' => 'medium'
            ]);

        $services->set('dashboard.widget.taskqueue.failedTasks')
            ->class(BarChartWidget::class)
            ->arg('$dataProvider', new Reference(FailedTasksProvider::class))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', null)
            ->arg('$options', ['refreshAvailable' => true])
            ->tag('dashboard.widget', [
                'identifier' => 'failedtasks',
                'groupNames' => 'systemInfo',
                'title' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.failedtasks.title',
                'description' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.failedtasks.description',
                'iconIdentifier' => 'tx-taskqueue-status-failed',
                'height' => 'medium',
                'width' => 'medium'
            ]);

        $services->set('dashboard.widget.taskqueue.finishedTasks')
            ->class(BarChartWidget::class)
            ->arg('$dataProvider', new Reference(FinishedTasksProvider::class))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', null)
            ->arg('$options', ['refreshAvailable' => true])
            ->tag('dashboard.widget', [
                'identifier' => 'finishedtasks',
                'groupNames' => 'systemInfo',
                'title' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.finishedtasks.title',
                'description' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.finishedtasks.description',
                'iconIdentifier' => 'tx-taskqueue-status-finished',
                'height' => 'medium',
                'width' => 'medium'
            ]);

        $services->set('dashboard.widget.taskqueue.icomingTasks')
            ->class(BarChartWidget::class)
            ->arg('$dataProvider', new Reference(IncomingTasksProvider::class))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', null)
            ->arg('$options', ['refreshAvailable' => true])
            ->tag('dashboard.widget', [
                'identifier' => 'incomingtasks',
                'groupNames' => 'systemInfo',
                'title' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.incomingtasks.title',
                'description' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.incomingtasks.description',
                'height' => 'medium',
                'width' => 'medium'
            ]);

        $services->set('dashboard.widget.taskqueue.taskThroughput')
            ->class(BarChartWidget::class)
            ->arg('$dataProvider', new Reference(TaskThroughputProvider::class))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', null)
            ->arg('$options', ['refreshAvailable' => true])
            ->tag('dashboard.widget', [
                'identifier' => 'taskthroughput',
                'groupNames' => 'systemInfo',
                'title' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.taskthroughput.title',
                'description' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.taskthroughput.description',
                'height' => 'medium',
                'width' => 'medium'
            ]);

        $services->set('dashboard.widget.taskqueue.waitingTasks')
            ->class(BarChartWidget::class)
            ->arg('$dataProvider', new Reference(WaitingTasksProvider::class))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', null)
            ->arg('$options', ['refreshAvailable' => true])
            ->tag('dashboard.widget', [
                'identifier' => 'waitingtasks',
                'groupNames' => 'systemInfo',
                'title' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.waitingtasks.title',
                'description' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang.xlf:widgets.taskqueue.waitingtasks.description',
                'iconIdentifier' => 'tx-taskqueue-status-waiting',
                'height' => 'medium',
                'width' => 'medium'
            ]);
    }

};
