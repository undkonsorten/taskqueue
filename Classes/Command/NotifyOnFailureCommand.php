<?php

namespace Undkonsorten\Taskqueue\Command;

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

class NotifyOnFailureCommand extends Command
{
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

    }

    protected function execute(InputInterface $input, OutputInterface $output)
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

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_taskqueue_domain_model_task');
        $failedTasks = $queryBuilder
            ->select('*')
            ->from('tx_taskqueue_domain_model_task')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($input->getOption('name'))),
                    $queryBuilder->expr()->eq('status', $queryBuilder->createNamedParameter(Task::FAILED)),
                    $queryBuilder->expr()->gte('tstamp', $queryBuilder->createNamedParameter($maximumTimestamp))
                )
            )
            ->execute()
            ->rowCount();
        if($failedTasks >= $input->getOption('count')){
            /** @var MailMessage $mail */
            $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);
            $from = \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();
            $mail->setSubject('There are more than '.$input->getOption('count').' failed tasks.');
            $mail->setFrom($from);
            $mail->setTo([$input->getOption('email')]);
            $mail->setBody('There are '.$failedTasks.' failed tasks since '.date('Y-m-d H:i',$maximumTimestamp).' , you might want to check that.');
            $mail->send();
        }
        return 0;
    }

}
