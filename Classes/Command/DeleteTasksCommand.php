<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Command;

use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

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
class DeleteTasksCommand extends Command
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
        $this->addArgument('keepDateInterval',InputArgument::OPTIONAL, 'Date interval of tasks to be kept.','P3M');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws IllegalObjectTypeException
     * @throws InvalidQueryException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tasks = $this->taskRepository->findOutOfInterval(new \DateInterval($input->getArgument('keepDateInterval')));
        if ($tasks->count() === 0) {
            $output->writeln("<info>No tasks found older than " . $input->getArgument('keepDateInterval') . "</info>");
        } else {
            foreach ($tasks as $task) {
                $this->taskRepository->remove($task);
                $output->writeln("<info>Task " . $task->getName() . " has been deleted.</info>", OutputInterface::VERBOSITY_VERY_VERBOSE);
            }
            $output->writeln(
                sprintf(
                    "<info>%d tasks found older than %s were deleted</info>",
                    $tasks->count(),
                    $input->getArgument('keepDateInterval')
                ),
                OutputInterface::VERBOSITY_VERBOSE
            );
        }
        $this->persistenceManager->persistAll();
        return 0;
    }

}
