<?php
return [
    'tx-taskqueue-status-finished' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/green.png',
    ],
    'tx-taskqueue-status-failed' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/red.png',
    ],
    'tx-taskqueue-status-waiting' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/blue.png',
    ],
    'tx-taskqueue-status-running' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        'source' => 'EXT:taskqueue/Resources/Public/Icons/yellow.png',
    ],
];
