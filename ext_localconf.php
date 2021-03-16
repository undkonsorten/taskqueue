<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Undkonsorten\Taskqueue\Controller\TaskQueueCommandController::class;
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:taskqueue/Configuration/TypoScript/constants.txt"');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:taskqueue/Configuration/TypoScript/setup.txt"');
}
