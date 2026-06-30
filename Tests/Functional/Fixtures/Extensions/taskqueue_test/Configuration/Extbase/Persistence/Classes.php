<?php

declare(strict_types=1);

return [
    \Undkonsorten\TaskqueueTest\Domain\Model\TestTask::class => [
        'tableName' => 'tx_taskqueue_domain_model_task',
        'recordType' => \Undkonsorten\TaskqueueTest\Domain\Model\TestTask::class,
    ],
];
