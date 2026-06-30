<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'tx-taskqueue-status-finished' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/finished.svg',
    ],
    'tx-taskqueue-status-failed' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/failed.svg',
    ],
    'tx-taskqueue-status-waiting' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/waiting.svg',
    ],
    'tx-taskqueue-status-running' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/running.svg',
    ],
];
