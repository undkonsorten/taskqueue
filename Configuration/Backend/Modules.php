<?php

return [
    'taskqueue' => [
        'parent' => 'tools',
        #'position' => ['after' => 'scheduler'],
        'workspaces' => 'scheduler',
        'path' => '/module/page/taskqueue',
        'access' => 'systemMaintainer',
        'icon' => 'EXT:taskqueue/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:taskqueue/Resources/Private/Language/locallang_taskqueue.xlf',
        'extensionName' => 'Taskqueue',
        'controllerActions' => [
            \Undkonsorten\Taskqueue\Controller\TaskController::class => [
                'list', 'show', 'delete', 'run', 'deleteFinished', 'deleteFailed', 'reactivate', 'search', 'searchUid', 'searchResult',
            ],
        ],
    ]
];
