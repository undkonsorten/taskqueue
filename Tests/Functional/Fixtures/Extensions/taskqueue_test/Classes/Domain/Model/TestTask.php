<?php

declare(strict_types=1);

namespace Undkonsorten\TaskqueueTest\Domain\Model;

use Undkonsorten\Taskqueue\Domain\Model\Task;

/**
 * Minimal concrete Task implementation used exclusively in tests.
 * Behaviour can be controlled via the $shouldFail and $shouldThrow flags.
 */
class TestTask extends Task
{
    public bool $wasRun = false;

    public bool $shouldFail = false;

    public ?\Throwable $shouldThrow = null;

    public function run(): void
    {
        $this->wasRun = true;
        if ($this->shouldThrow !== null) {
            throw $this->shouldThrow;
        }
    }
}
