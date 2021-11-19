<?php
declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Widget;

use FriendsOfTYPO3\Dashboard\Widgets\AbstractLineChartWidget;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;


class TaskqueueThroughputWidget extends AbstractTaskqueueWidget
{
    /**
     * @var string
     */
    protected $title = 'Tasks throughput';

    protected $datefield = 'tstamp';

    protected $status = TaskInterface::FINISHED;

}
