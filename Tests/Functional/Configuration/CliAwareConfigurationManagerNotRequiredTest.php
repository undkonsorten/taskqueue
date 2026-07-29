<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Functional\Configuration;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;
use Undkonsorten\TaskqueueTest\Domain\Model\TestTask;

/**
 * "undkonsorten/extbase-cli-aware-configuration-manager" was originally added because Extbase
 * repositories used to throw "No request given. ConfigurationManager has not been initialized
 * properly." when used outside an HTTP request (e.g. from console commands).
 *
 * TYPO3 core (>=13.4 / >=14.3) now catches that NoServerRequestGivenException itself inside
 * TYPO3\CMS\Extbase\Persistence\Generic\Backend and QueryFactory and falls back to storagePid 0,
 * so plain Extbase persistence (find/add/update/remove/persistAll) works without a request and
 * without the extbase-cli-aware-configuration-manager package. This test documents/pins that
 * behaviour so the dependency can be dropped for TYPO3 13/14 without regressing CLI usage
 * (taskqueue:run-tasks, taskqueue:delete-tasks, etc.).
 */
#[CoversNothing]
final class CliAwareConfigurationManagerNotRequiredTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/taskqueue-test',
    ];

    #[Test]
    public function configurationManagerInterfaceResolvesToCoreDefaultImplementation(): void
    {
        $configurationManager = $this->get(ConfigurationManagerInterface::class);

        self::assertInstanceOf(ConfigurationManager::class, $configurationManager);
    }

    #[Test]
    public function repositoryCrudWorksWithoutARequestBeingSet(): void
    {
        self::assertNull($GLOBALS['TYPO3_REQUEST'] ?? null);

        $taskRepository = $this->get(TaskRepository::class);
        $persistenceManager = $this->get(PersistenceManagerInterface::class);

        $task = new TestTask();
        $task->setName(TestTask::class);
        $task->setData([]);

        $taskRepository->add($task);
        $persistenceManager->persistAll();
        self::assertGreaterThan(0, $task->getUid());

        $task->markRunning();
        $taskRepository->update($task);
        $persistenceManager->persistAll();

        $persisted = $taskRepository->findByUid($task->getUid());
        self::assertNotNull($persisted);

        $taskRepository->remove($persisted);
        $persistenceManager->persistAll();

        self::assertNull($taskRepository->findByUid($task->getUid()));
    }

    /**
     * ext_typoscript_setup.typoscript configures `module.tx_taskqueue.persistence.storagePid`,
     * which TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager only ever reads for a
     * real backend HTTP request. In CLI (no request), TYPO3\CMS\Extbase\Persistence\Generic\Backend
     * falls back to pid 0 for new records instead of consulting that TypoScript. Since
     * TaskRepository disables storage-page respecting for querying (see initializeObject()), a
     * task landing on pid 0 is still fully found/run/removed by the queue - the module TypoScript
     * setting only ever applied to the backend module UI, never to CLI task processing.
     */
    #[Test]
    public function newTaskFallsBackToPidZeroInCliContextAndIsStillFullyUsable(): void
    {
        $taskRepository = $this->get(TaskRepository::class);
        $persistenceManager = $this->get(PersistenceManagerInterface::class);

        $task = new TestTask();
        $task->setName(TestTask::class);
        $task->setData([]);

        $taskRepository->add($task);
        $persistenceManager->persistAll();

        self::assertSame(0, $task->getPid());

        $found = $taskRepository->findRunableTasks();
        self::assertSame(1, $found->count());
    }
}
