<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Command;

use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;
use Undkonsorten\Taskqueue\Exception\StopRunException;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Eike Starkmann <starkmann@undkonsorten.com>, undkonsorten
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class ReactivateTasksCommand extends Command
{

    /**
     * taskRepository
     *
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @var string
     */
    protected $skipTaskname = "";

    public function injectTaskRepository(TaskRepository $taskRepository): void
    {
        $this->taskRepository = $taskRepository;
    }

    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager): void
    {
        $this->persistenceManager = $persistenceManager;
    }


    protected function configure()
    {
        $this->setDescription('Set failed tasks to waiting (reactivate failed tasks)');
        $this->addArgument('dateInterval',InputArgument::OPTIONAL, 'Date interval of tasks to be reactivated. (default last three months (https://en.wikipedia.org/wiki/ISO_8601#Durations))','P3M');
        parent::configure(); // TODO: Change the autogenerated stub
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws IllegalObjectTypeException
     * @throws InvalidQueryException
     * @throws UnknownObjectException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $globalTime = microtime(true);
        $failedTasks = $this->taskRepository->findFailedOutOfInterval(new \DateInterval($input->getArgument('dateInterval')));
        foreach ($failedTasks as $task){
            /** @var Task $task*/
            $task->setRetries(3);
            $task->setStatus(Task::RETRY);
            $this->taskRepository->update($task);
        }
        /** @noinspection DisconnectedForeachInstructionInspection */
        $this->persistenceManager->persistAll();
        $globalTimeUsed = microtime(true) - $globalTime;
        $output->writeln(
            sprintf('<info>%d tasks have been processed', $failedTasks->count()),
            OutputInterface::VERBOSITY_VERBOSE
        );
        $output->writeln("Time used: ".$globalTimeUsed,OutputInterface::VERBOSITY_VERBOSE);
        return 0;
    }
}
