<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'Taskqueue Test Fixture Extension',
    'description' => 'Provides a concrete Task subclass for functional tests.',
    'category' => 'misc',
    'state' => 'excludeFromUpdates',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'taskqueue' => '',
        ],
    ],
];
