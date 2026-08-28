<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Functional\Controller;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\Taskqueue\Controller\TaskController;

/**
 * Regression test for the "TaskController service or alias has been removed or inlined when
 * the container was compiled" error: `Configuration/Backend/Modules.php` wires the "taskqueue"
 * backend module's `controllerActions` directly to `TaskController::class`, so TYPO3's backend
 * module dispatcher fetches it straight from the container. When the service definition is
 * excluded from registration (as it was before this fix, on TYPO3 13), Symfony's compiler
 * strips it entirely as unused - it disappears from both the public and the "private" container
 * (`FunctionalTestCase::has()` checks both), which is exactly what `assertTrue` below pins.
 *
 * Deliberately uses `has()`, not `get()`: fully constructing an ActionController through the
 * container outside of an actual HTTP request fails for an unrelated reason (Extbase's
 * ConfigurationManager requires a current request, see
 * Tests/Functional/Configuration/CliAwareConfigurationManagerNotRequiredTest.php) - `has()`
 * checks the DI registration/visibility itself without needing a request context.
 *
 * A unit test cannot catch this because it never touches the container, and
 * Tests/Unit/Controller/TaskControllerTest.php specifically bypasses the constructor.
 */
#[CoversNothing]
final class TaskControllerContainerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
    ];

    #[Test]
    public function taskControllerIsRegisteredInTheContainer(): void
    {
        self::assertTrue($this->has(TaskController::class));
    }
}
