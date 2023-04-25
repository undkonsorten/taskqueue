<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */
if (!defined('TYPO3')) {
    die('Access denied.');
}
if (TYPO3 === 'BE') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:taskqueue/Configuration/TypoScript/constants.txt"');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:taskqueue/Configuration/TypoScript/setup.txt"');
}


