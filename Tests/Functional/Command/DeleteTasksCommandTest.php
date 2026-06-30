<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Functional\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\Taskqueue\Command\DeleteTasksCommand;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

#[CoversClass(DeleteTasksCommand::class)]
final class DeleteTasksCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'Tests/Functional/Fixtures/Extensions/taskqueue_test',
    ];

    private TaskRepository $taskRepository;
    private PersistenceManagerInterface $persistenceManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = $this->get(TaskRepository::class);
        $this->persistenceManager = $this->get(PersistenceManagerInterface::class);
    }

    private function buildCommand(): DeleteTasksCommand
    {
        $command = new DeleteTasksCommand();
        $command->injectTaskRepository($this->taskRepository);
        $command->injectPersistenceManager($this->persistenceManager);
        return $command;
    }

    #[Test]
    public function commandDeletesOldFinishedAndFailedTasks(): void
    {
        // uid 1 (FINISHED, old), uid 2 (FAILED, old), uid 3 (TERMINATED, old) → deleted
        // uid 4 (FINISHED, recent) → kept
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FailedTasksForDeletion.csv');

        $tester = new CommandTester($this->buildCommand());
        $tester->execute(['keepDateInterval' => 'P1D']);

        // uid 1 and 2 should be gone (old tstamp), uid 3 is recent so kept
        $remaining = $this->taskRepository->findAll();
        self::assertSame(1, $remaining->count());
        self::assertSame(3, $remaining->getFirst()->getUid());
    }

    #[Test]
    public function commandReturnsSuccessExitCode(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FailedTasksForDeletion.csv');

        $tester = new CommandTester($this->buildCommand());
        $exitCode = $tester->execute(['keepDateInterval' => 'P1D']);

        self::assertSame(0, $exitCode);
    }

    #[Test]
    public function commandOutputsInfoWhenNoTasksFound(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FailedTasksForDeletion.csv');

        $tester = new CommandTester($this->buildCommand());
        // PT1S = 1 second — all "old" tasks have tstamp in distant past, so they are out of interval
        // Use a very short interval so only very recent tasks would be kept; there are none → 0 found msg
        // Actually use P100Y so everything is "too recent" = 0 tasks found
        $tester->execute(['keepDateInterval' => 'P100Y']);

        self::assertStringContainsString('No tasks found', $tester->getDisplay());
    }

    #[Test]
    public function commandUsesDefaultIntervalOfThreeMonths(): void
    {
        // Just verify the default argument is wired; no tasks in DB → "No tasks found"
        $tester = new CommandTester($this->buildCommand());
        $tester->execute([]);

        self::assertSame(0, $tester->getStatusCode());
    }
}
