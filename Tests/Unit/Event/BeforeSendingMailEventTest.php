<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\Taskqueue\Event\BeforeSendingMailEvent;

#[CoversClass(BeforeSendingMailEvent::class)]
final class BeforeSendingMailEventTest extends UnitTestCase
{
    #[Test]
    public function getMailMessageReturnsConstructorArgument(): void
    {
        $mail = $this->createMock(MailMessage::class);
        $input = new ArrayInput([], new InputDefinition());

        $event = new BeforeSendingMailEvent($mail, 3, $input);

        self::assertSame($mail, $event->getMailMessage());
    }

    #[Test]
    public function getFailedTasksReturnsConstructorArgument(): void
    {
        $mail = $this->createMock(MailMessage::class);
        $input = new ArrayInput([], new InputDefinition());

        $event = new BeforeSendingMailEvent($mail, 7, $input);

        self::assertSame(7, $event->getFailedTasks());
    }

    #[Test]
    public function getInputOptionsReturnsConstructorArgument(): void
    {
        $mail = $this->createMock(MailMessage::class);
        $input = new ArrayInput([], new InputDefinition());

        $event = new BeforeSendingMailEvent($mail, 1, $input);

        self::assertSame($input, $event->getInputOptions());
    }

    #[Test]
    public function setMailMessageReplacesMessage(): void
    {
        $original = $this->createMock(MailMessage::class);
        $replacement = $this->createMock(MailMessage::class);
        $input = new ArrayInput([], new InputDefinition());

        $event = new BeforeSendingMailEvent($original, 1, $input);
        $event->setMailMessage($replacement);

        self::assertSame($replacement, $event->getMailMessage());
    }
}
