<?php

return [
    'taskqueue:runtasks' => [
        'class' => Undkonsorten\Taskqueue\Command\RunTasksCommand::class,
    ],
    'taskqueue:deletetasks' => [
        'class' => Undkonsorten\Taskqueue\Command\DeleteTasksCommand::class,
    ]
];
