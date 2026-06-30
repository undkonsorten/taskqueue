<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Functional\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\Taskqueue\Command\NotifyOnFailureCommand;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;
use Undkonsorten\Taskqueue\Event\BeforeSendingMailEvent;

#[CoversClass(NotifyOnFailureCommand::class)]
final class NotifyOnFailureCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'Tests/Functional/Fixtures/Extensions/taskqueue_test',
    ];

    #[Test]
    public function commandDoesNotSendMailWhenCountBelowThreshold(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FailedTasksForDeletion.csv');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $command = new NotifyOnFailureCommand($mailer);
        $command->injectEventDispatcher($dispatcher);

        $tester = new CommandTester($command);
        $tester->execute([
            '--name' => 'Undkonsorten\TaskqueueTest\Domain\Model\TestTask',
            '--count' => 100,
            '--email' => 'admin@example.com',
        ]);

        self::assertSame(0, $tester->getStatusCode());
    }

    #[Test]
    public function commandSendsMailWhenCountReachesThreshold(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/FailedTasksForDeletion.csv');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send');

        $dispatchedEvent = null;
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(function ($event) use (&$dispatchedEvent) {
                $dispatchedEvent = $event;
                return $event;
            });

        $command = new NotifyOnFailureCommand($mailer);
        $command->injectEventDispatcher($dispatcher);

        $tester = new CommandTester($command);
        // There are 2 FAILED tasks (uid 1,2) with name matching; threshold = 1
        $tester->execute([
            '--name' => 'Undkonsorten\TaskqueueTest\Domain\Model\TestTask',
            '--count' => 1,
            '--email' => 'admin@example.com',
            '--status' => (string)TaskInterface::FAILED,
        ]);

        self::assertInstanceOf(BeforeSendingMailEvent::class, $dispatchedEvent);
        self::assertSame(0, $tester->getStatusCode());
    }

    #[Test]
    public function commandReturnsSuccessExitCode(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $command = new NotifyOnFailureCommand($mailer);
        $command->injectEventDispatcher($dispatcher);

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            '--email' => 'admin@example.com',
            '--count' => 999,
        ]);

        self::assertSame(0, $exitCode);
    }
}
