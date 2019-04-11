<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'task',
    'description' => 'Provides a task queue for other extensions to use.',
    'category' => 'module',
    'author' => 'Eike Starkmann',
    'author_email' => 'starkmann@undkonsorten.com',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '5.0.4',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
