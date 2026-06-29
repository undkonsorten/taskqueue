<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

/** @noinspection PhpFullyQualifiedNameUsageInspection */

use TYPO3\CMS\Core\Core\Environment;

if (!defined('TYPO3')) {
    die('Access denied.');
}
if (TYPO3 === 'BE') {
    ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:taskqueue/Configuration/TypoScript/constants.txt"');
    ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:taskqueue/Configuration/TypoScript/setup.txt"');
}


if (getenv('TASKQUEUE_MINIMUM_LOGLEVEL')) {
    $GLOBALS['TYPO3_CONF_VARS']['LOG']['Undkonsorten']['Taskqueue'] = [
        'writerConfiguration' => [
            getenv('TASKQUEUE_MINIMUM_LOGLEVEL') => [
                FileWriter::class => [
                    // configuration for the writer
                    'logFile' => Environment::getVarPath() . '/log/taskqueue.log'
                ]
            ]
        ]
    ];
}
