<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

use TYPO3\CMS\Core\Core\Environment;

if (!defined('TYPO3')) {
    die('Access denied.');
}
if (TYPO3 === 'BE') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:taskqueue/Configuration/TypoScript/constants.txt"');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:taskqueue/Configuration/TypoScript/setup.txt"');
}


if (getenv('TASKQUEUE_MINIMUM_LOGLEVEL')) {
    $GLOBALS['TYPO3_CONF_VARS']['LOG']['Undkonsorten']['Taskqueue'] = [
        'writerConfiguration' => [
            getenv('TASKQUEUE_MINIMUM_LOGLEVEL') => [
                \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                    // configuration for the writer
                    'logFile' => Environment::getVarPath() . '/log/taskqueue.log'
                ]
            ]
        ]
    ];
}
