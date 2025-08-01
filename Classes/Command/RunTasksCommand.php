<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Command;

use ErrorException;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;
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
class RunTasksCommand extends Command implements SignalableCommandInterface
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

    /**
     * @var TaskInterface
     */
    protected TaskInterface $currentTask;

    public function __construct(?string $name = null)
    {
        register_shutdown_function([&$this, "shutdown"]);
        parent::__construct($name);
    }

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
     * @throws IllegalObjectTypeException
     * @throws InvalidQueryException
     * @throws UnknownObjectException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $globalTime = microtime(true);
        $tasks = $this->taskRepository->findRunableTasks(
            $input->getArgument('limit'),
            $input->getArgument('whitelist'),
            $input->getArgument('blacklist')
        );
        foreach ($tasks as $task) {
            /**@var $task Task* */
            if($task->getName() !== $this->skipTaskname) {
                try {
                    $start = microtime(true);
                    $this->currentTask = $task;
                    $task->setRetries($task->getRetries() - 1);
                    $task->markRunning();
                    $this->taskRepository->update($task);
                    $this->persistenceManager->persistAll();
                    $task->run();
                    $task->markFinished();
                    $usedTime = microtime(true) - $start;
                    $output->writeln("Task finished in ".$usedTime,OutputInterface::VERBOSITY_VERBOSE);
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
                    } catch (\Throwable $exception) {
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
        $globalTimeUsed = microtime(true) - $globalTime;
        $output->writeln(
            sprintf('<info>%d tasks have been processed', $tasks->count()),
            OutputInterface::VERBOSITY_VERBOSE
        );
        $output->writeln("Time used: ".$globalTimeUsed,OutputInterface::VERBOSITY_VERBOSE);
        return 0;
    }

    public function getSubscribedSignals(): array
    {
        if(extension_loaded('pcntl')){
            return [
                SIGINT,
                SIGTERM,
            ];
        }
        return [];
    }

    public function handleSignal(int $signal, false|int $previousExitCode = 0): int|false
    {
        try{
            $this->currentTask->setStatus(TaskInterface::TERMINATED);
            $this->currentTask->setMessage("Process was signaled with ".$signal);
            $this->taskRepository->update($this->currentTask);
            $this->persistenceManager->persistAll();
        }catch(\Throwable $throwable){
            return self::FAILURE;
        }
        return $previousExitCode;
    }

    public function shutdown(): void
    {
        $error = error_get_last();
        if(!is_null($error)){
            $this->currentTask->setStatus(TaskInterface::TERMINATED);
            $this->currentTask->setMessage($error['message'] ?? "Process had a fatal error.");
            $this->taskRepository->update($this->currentTask);
            $this->persistenceManager->persistAll();
        }
    }
}
