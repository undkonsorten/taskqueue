<?php
declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Widget\Provider;

use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;


class WaitingTasksProvider extends AbstractTaskqueueProvider
{
    /**
     * @var string
     */
    protected $title = 'Waiting tasks by entry date';

    protected $datefield = 'crdate';

    protected $status = TaskInterface::WAITING;

}
