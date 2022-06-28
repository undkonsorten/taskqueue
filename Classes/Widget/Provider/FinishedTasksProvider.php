<?php
declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Widget\Provider;

use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;


class FinishedTasksProvider extends AbstractTaskqueueProvider
{
    /**
     * @var string
     */
    protected $title = 'Finished tasks by entry date';

    protected $datefield = 'crdate';

    protected $status = TaskInterface::FINISHED;

}
