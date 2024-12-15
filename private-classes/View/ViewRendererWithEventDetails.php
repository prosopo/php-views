<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Closure;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewRendererWithEventDetails implements ViewRendererInterface
{
    private ViewRendererInterface $viewRenderer;
    private EventDispatcherInterface $eventDispatcher;
    private string $eventName;

    public function __construct(
        ViewRendererInterface $viewRenderer,
        EventDispatcherInterface $eventDispatcher,
        string $eventName
    ) {
        $this->viewRenderer = $viewRenderer;
        $this->eventDispatcher = $eventDispatcher;
        $this->eventName = $eventName;
    }

    public function renderView($viewOrClass, Closure $setupCallback = null, bool $doPrint = false): string
    {
        $viewClass = true === is_string($viewOrClass) ?
            $viewOrClass :
            get_class($viewOrClass);

        $eventDetails = [
            'viewClass' => $viewClass,
        ];

        $this->eventDispatcher->attachEventDetails($this->eventName, $eventDetails);

        $response = $this->viewRenderer->renderView($viewOrClass, $setupCallback, $doPrint);

        $this->eventDispatcher->detachEventDetails($this->eventName, $eventDetails);

        return $response;
    }
}
