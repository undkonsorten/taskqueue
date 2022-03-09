<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */
if (!defined('TYPO3')) {
    die('Access denied.');
}
if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:taskqueue/Configuration/TypoScript/constants.txt"');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:taskqueue/Configuration/TypoScript/setup.txt"');
}
if(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dashboard')){
    $widgetRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\FriendsOfTYPO3\Dashboard\Registry\WidgetRegistry::class);
    $widgetRegistry->registerWidget('taskqueue_finished', \Undkonsorten\Taskqueue\Widget\FinishedTasksWidget::class);
    $widgetRegistry->registerWidget('taskqueue_failed', \Undkonsorten\Taskqueue\Widget\FailedTasksWidget::class);
    $widgetRegistry->registerWidget('taskqueue_waiting', \Undkonsorten\Taskqueue\Widget\WaitingTasksWidget::class);
    $widgetRegistry->registerWidget('taskqueue_throughput', \Undkonsorten\Taskqueue\Widget\TaskThroughputWidget::class);
    $widgetRegistry->registerWidget('taskqueue_incoming', \Undkonsorten\Taskqueue\Widget\IncomingTasksWidget::class);
    $widgetRegistry->registerWidget('taskqueue_latest', \Undkonsorten\Taskqueue\Widget\LatestTasksWidget::class);
}


