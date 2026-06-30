<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Functional\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\Taskqueue\Command\ReactivateTasksCommand;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

#[CoversClass(ReactivateTasksCommand::class)]
final class ReactivateTasksCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/taskqueue-test',
    ];

    private TaskRepository $taskRepository;
    private PersistenceManagerInterface $persistenceManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = $this->get(TaskRepository::class);
        $this->persistenceManager = $this->get(PersistenceManagerInterface::class);
    }

    private function buildCommand(): ReactivateTasksCommand
    {
        $command = new ReactivateTasksCommand();
        $command->injectTaskRepository($this->taskRepository);
        $command->injectPersistenceManager($this->persistenceManager);
        return $command;
    }

    #[Test]
    public function recentlyFailedTaskIsReactivatedToRetryStatus(): void
    {
        // uid 1: FAILED, recent crdate (9000000000 ≈ far future timestamp ensures it's within any interval)
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FailedTasksForReactivation.csv');

        $tester = new CommandTester($this->buildCommand());
        // P100Y covers everything
        $tester->execute(['dateInterval' => 'P100Y']);

        $task = $this->taskRepository->findByUid(1);
        self::assertSame(TaskInterface::RETRY, $task->getStatus());
        self::assertSame(3, $task->getRetries());
    }

    #[Test]
    public function commandReturnsSuccessExitCode(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FailedTasksForReactivation.csv');

        $tester = new CommandTester($this->buildCommand());
        $exitCode = $tester->execute(['dateInterval' => 'P100Y']);

        self::assertSame(0, $exitCode);
    }

    #[Test]
    public function taskOutsideIntervalIsNotReactivated(): void
    {
        // uid 2: FAILED with very old crdate (100000 ≈ 1970)
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FailedTasksForReactivation.csv');

        $tester = new CommandTester($this->buildCommand());
        // P1D: only tasks created within the last day should be reactivated
        $tester->execute(['dateInterval' => 'P1D']);

        $task = $this->taskRepository->findByUid(2);
        // uid 2 has crdate=100000 (very old), so it is outside P1D and should remain FAILED
        self::assertSame(TaskInterface::FAILED, $task->getStatus());
    }
}
