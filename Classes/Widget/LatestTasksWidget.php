<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace  Undkonsorten\Taskqueue\Widget;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

/**
 * Concrete List Widget implementation
 *
 * The widget will show a simple list with items provided by a data provider. You can add a button to the widget by
 * defining a button provider.
 *
 * There are no options available for this widget
 *
 * @see ListDataProviderInterface
 * @see ButtonProviderInterface
 */
class LatestTasksWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    /**
     * @param ButtonProviderInterface|null $buttonProvider
     */
    public function __construct(private readonly WidgetConfigurationInterface $configuration, private readonly ListDataProviderInterface $dataProvider, private readonly BackendViewFactory $backendViewFactory, private $buttonProvider = null, private readonly array $options = [])
    {
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request,['typo3/cms-dashboard', 'undkonsorten/taskqueue']);
        $view->assignMultiple([
            'items' => $this->getItems(),
            'options' => $this->options,
            'button' => $this->buttonProvider,
            'configuration' => $this->configuration,
        ]);
        return $view->render('Widget/LatestTasks');
    }

    protected function getItems(): array
    {
        return $this->dataProvider->getItems();
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}
