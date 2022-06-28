<?php
declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Widget\Provider;

use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;


class FailedTasksProvider extends AbstractTaskqueueProvider
{
    /**
     * @var string
     */
    protected $title = 'Failed by tasks by entry date';

    protected $datefield = 'crdate';

    protected $status = TaskInterface::FAILED;

}
