<?php

declare(strict_types=1);

namespace Undkonsorten\Taskqueue\Tests\Unit\ViewHelpers\Link;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\Taskqueue\ViewHelpers\Link\EditRecordViewHelper;

#[CoversClass(EditRecordViewHelper::class)]
final class EditRecordViewHelperTest extends UnitTestCase
{
    #[Test]
    public function renderThrowsForUidZero(): void
    {
        $uriBuilder = $this->createMock(UriBuilder::class);
        $viewHelper = new EditRecordViewHelper($uriBuilder);

        // Initialize the tag object that AbstractTagBasedViewHelper needs
        $renderingContext = $this->getMockBuilder(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface::class)
            ->getMock();
        $viewHelper->setRenderingContext($renderingContext);
        $viewHelper->initializeArguments();
        $viewHelper->setArguments(['uid' => 0, 'table' => 'tx_foo', 'returnUrl' => '/']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1526127158);
        $viewHelper->render();
    }

    #[Test]
    public function renderThrowsForNegativeUid(): void
    {
        $uriBuilder = $this->createMock(UriBuilder::class);
        $viewHelper = new EditRecordViewHelper($uriBuilder);

        $renderingContext = $this->getMockBuilder(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface::class)
            ->getMock();
        $viewHelper->setRenderingContext($renderingContext);
        $viewHelper->initializeArguments();
        $viewHelper->setArguments(['uid' => -5, 'table' => 'tx_foo', 'returnUrl' => '/']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1526127158);
        $viewHelper->render();
    }
}
