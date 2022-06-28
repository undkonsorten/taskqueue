<?php
declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Widget\Provider;

use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;


class TaskThroughputProvider extends AbstractTaskqueueProvider
{
    /**
     * @var string
     */
    protected $title = 'Tasks throughput';

    protected $datefield = 'tstamp';

    protected $status = TaskInterface::FINISHED;

}
