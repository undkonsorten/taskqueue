<?php

namespace Undkonsorten\Taskqueue\Event;

use Symfony\Component\Console\Input\InputInterface;
use TYPO3\CMS\Core\Mail\MailMessage;

class BeforeSendingMailEvent
{
    public function __construct(
        private MailMessage $mailMessage,
        private readonly int $failedTasks,
        private readonly InputInterface $inputOptions,
    ) {}

    public function getMailMessage(): MailMessage
    {
        return $this->mailMessage;
    }

    public function setMailMessage(MailMessage $mailMessage): void
    {
        $this->mailMessage = $mailMessage;
    }

    public function getFailedTasks(): int
    {
        return $this->failedTasks;
    }

    public function getInputOptions(): InputInterface
    {
        return $this->inputOptions;
    }
}
