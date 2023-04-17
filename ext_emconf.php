<?php

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Taskqueue',
    'description' => 'Provides a task queue for other extensions to use.',
    'category' => 'module',
    'author' => 'Eike Starkmann',
    'author_email' => 'starkmann@undkonsorten.com',
    'state' => 'stable',
    'version' => '8.0.7',
    'constraints' => [
        'depends' => [
            'typo3' => '11.4.0-11.99.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
