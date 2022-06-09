<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Command;

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
class RunTasksCommand extends Command
{

    /**
     * taskRepository
     *
     * @var \Undkonsorten\Taskqueue\Domain\Repository\TaskRepository
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
        $this->addArgument('limit',InputArgument::OPTIONAL, 'How many tasks should be executed in one run',10);
        $this->addArgument('whitelist',InputArgument::OPTIONAL, 'Whitelist of tasks');
        $this->addArgument('blacklist',InputArgument::OPTIONAL, 'Blacklist of tasks');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tasks = $this->taskRepository->findRunableTasks(
            $input->getArgument('limit'),
            $input->getArgument('whitelist'),
            $input->getArgument('blacklist')
        );
        foreach ($tasks as $task) {
            if($task->getName() !== $this->skipTaskname) {
                /**@var $task Task* */
                try {
                    /**@var Task $task * */

                    $task->setRetries($task->getRetries() - 1);
                    $task->markRunning();
                    $this->taskRepository->update($task);
                    $this->persistenceManager->persistAll();
                    $task->run();
                    $task->markFinished();
                }
                catch
                    (StopRunException $exception ){
                        $task->setMessage($exception->getMessage());
                        if ($task->getRetries() === 0) {
                            $task->markFailed();
                        } else {
                            $task->markRetry();
                        }
                        $this->skipTaskname = $exception->getTaskname();
                    } catch (\Exception $exception) {
                        $task->setMessage($exception->getMessage());
                        if ($task->getRetries() === 0) {
                            $task->markFailed();
                        } else {
                            $task->markRetry();
                        }
                    }
            $this->taskRepository->update($task);
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->persistenceManager->persistAll();
            }
        }
        $output->writeln(
            sprintf('<info>%d tasks have been processed', $tasks->count()),
            OutputInterface::VERBOSITY_VERBOSE
        );
        return 0;
    }
}
