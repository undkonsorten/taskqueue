<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\Domain\Model\Fixtures;

use Undkonsorten\Taskqueue\Domain\Model\Task;

/**
 * Concrete Task for unit tests (no DB involved).
 */
final class ConcreteTask extends Task
{
    public function run(): void {}
}
