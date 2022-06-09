<?php
return [
    'tx-taskqueue-status-finished' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/finished.svg',
    ],
    'tx-taskqueue-status-failed' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/failed.svg',
    ],
    'tx-taskqueue-status-waiting' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/waiting.svg',
    ],
    'tx-taskqueue-status-running' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/running.svg',
    ],
];
