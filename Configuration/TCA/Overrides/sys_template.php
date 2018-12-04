<?php
declare(strict_types=1);
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('taskqueue', 'Configuration/TypoScript', 'task');
