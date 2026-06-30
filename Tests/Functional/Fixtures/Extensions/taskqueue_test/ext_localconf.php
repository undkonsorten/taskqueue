<?php

declare(strict_types=1);

if (!defined('TYPO3')) {
    die('Access denied.');
}

// Register the concrete TestTask type so Extbase can resolve it from the `type` column.
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\Undkonsorten\Taskqueue\Domain\Model\Task::class] = [
    'className' => \Undkonsorten\TaskqueueTest\Domain\Model\TestTask::class,
];
