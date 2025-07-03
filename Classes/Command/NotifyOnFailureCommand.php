<?php

namespace Undkonsorten\Taskqueue\Command;

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\MailUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;

class NotifyOnFailureCommand extends Command
{

    protected EventDispatcherInterface $eventDispatcher;

    public function injectEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure()
    {
        $this->setDescription('Sends notification about tasks.');
        $this->setHelp('Can be used to e.g. monitor task failures.');
        $this->addOption(
            'name',
            'na',
            InputOption::VALUE_OPTIONAL,
            'The name of the task to be watched.',
            ''
        );
        $this->addOption(
            'count',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Number of failed tasks.',
            10
        );
        $this->addOption(
            'email',
            'e',
            InputOption::VALUE_REQUIRED,
            'Email to send the notification to.'
        );

        $this->addOption(
            'interval',
            'i',
            InputOption::VALUE_OPTIONAL,
            'Date interval that should be respected.'
        );

        $this->addOption(
            'status',
            's',
            InputOption::VALUE_OPTIONAL,
            'Status of the task: 0|1|2|3|4'
        );

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        if(!is_null($input->getOption('interval'))){
            $minAge = $input->getOption('interval');
            $expiryDate = (new \DateTimeImmutable())->sub(new \DateInterval($minAge));
            $maximumTimestamp = $expiryDate->format('U');
        }else{
            $maximumTimestamp = 0;
        }
        if(!is_null($input->getOption('status'))){
            $status = $input->getOption('status');
        }else{
            $status = TaskInterface::FAILED;
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_taskqueue_domain_model_task');
        $failedTasks = $queryBuilder
            ->select('*')
            ->from('tx_taskqueue_domain_model_task')
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($input->getOption('name'))),
                    $queryBuilder->expr()->eq('status', $queryBuilder->createNamedParameter($status)),
                    $queryBuilder->expr()->gte('tstamp', $queryBuilder->createNamedParameter($maximumTimestamp))
                )
            )
            ->executeQuery()
            ->rowCount();
        if($failedTasks >= $input->getOption('count')){
            /** @var MailMessage $mail */
            $mail = GeneralUtility::makeInstance(MailMessage::class);
            $from = MailUtility::getSystemFrom();
            $mail->setSubject('There are more than '.$input->getOption('count').' tasks with status '.$status.'.');
            $mail->setFrom($from);
            $mail->setTo([$input->getOption('email')]);
            $mail->text('There are ' . $failedTasks . ' tasks with status ' . $status . ' since ' . date('Y-m-d H:i', $maximumTimestamp) . ' , you might want to check that.');
            $mail->send();
        }
        return 0;
    }

}
