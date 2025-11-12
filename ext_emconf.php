<?php

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Taskqueue',
    'description' => 'Provides a task queue for other extensions to use.',
    'category' => 'module',
    'author' => 'Eike Starkmann',
    'author_email' => 'starkmann@undkonsorten.com',
    'state' => 'stable',
    'version' => '9.2.6',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.99.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
