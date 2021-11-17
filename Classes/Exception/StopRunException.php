<?php
namespace Undkonsorten\Taskqueue\Exception;

use Throwable;
use TYPO3\CMS\Core\Exception;

class StopRunException extends Exception
{

    /**
     * @var string
     */
    protected $taskname;

    public function __construct($message = "", $code = 0, Throwable $previous = null, string $taskname)
    {
        $this->taskname = $taskname;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getTaskname(): string
    {
        return $this->taskname;
    }

    /**
     * @param string $taskname
     */
    public function setTaskname(string $taskname): void
    {
        $this->taskname = $taskname;
    }


}
