<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Taskqueue',
    'description' => 'Provides a task queue for other extensions to use.',
    'category' => 'module',
    'author' => 'Eike Starkmann',
    'author_email' => 'starkmann@undkonsorten.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '6.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.99.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
