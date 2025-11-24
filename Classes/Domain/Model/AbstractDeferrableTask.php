<?php

namespace Undkonsorten\Taskqueue\Domain\Model;

abstract class AbstractDeferrableTask extends Task
{

    public function enqueue(): self
    {
        if ($this->getStatus() !== TaskInterface::DEFERRED) {
            $message = sprintf(
                'Only previously deferred tasks can be enqueued. Tried to enqueue task %d with status %d',
                $this->uid,
                $this->getStatus()
            );
            throw new \UnexpectedValueException($message, 1749445299);
        }
        $this->setStatus(TaskInterface::WAITING);
        return $this;
    }

    public function defer(): self
    {
        if ($this->getStatus() !== TaskInterface::WAITING && $this->getStatus() !== TaskInterface::RETRY) {
            $message = sprintf(
                'Only waiting or retry tasks can be deferred. Tried to enqueue task %d with status %d',
                $this->uid,
                $this->getStatus()
            );
            throw new \UnexpectedValueException($message, 1749445300);
        }
        $this->setStatus(TaskInterface::DEFERRED);
        return $this;
    }
}
