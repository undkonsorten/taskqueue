<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Undkonsorten\Taskqueue\Controller\TaskQueueCommandController::class;
}
if(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dashboard')){
    $widgetRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\FriendsOfTYPO3\Dashboard\Registry\WidgetRegistry::class);
    $widgetRegistry->registerWidget('taskqueue_finished', \Undkonsorten\Taskqueue\Widget\TaskqueueFinishedWidget::class);
    $widgetRegistry->registerWidget('taskqueue_failed', \Undkonsorten\Taskqueue\Widget\TaskqueueFailedWidget::class);
}


