<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Model;

use Closure;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelRendererWithEventDetails implements ModelRendererInterface
{
    private ModelRendererInterface $viewRenderer;
    private EventDispatcherInterface $eventDispatcher;
    private string $eventName;

    public function __construct(
        ModelRendererInterface $viewRenderer,
        EventDispatcherInterface $eventDispatcher,
        string $eventName
    ) {
        $this->viewRenderer = $viewRenderer;
        $this->eventDispatcher = $eventDispatcher;
        $this->eventName = $eventName;
    }

    public function renderModel($modelOrClass, ?Closure $setupModelCallback = null): string
    {
        $modelClass = true === is_string($modelOrClass) ?
            $modelOrClass :
            get_class($modelOrClass);

        $eventDetails = [
            'modelClass' => $modelClass,
        ];

        $this->eventDispatcher->registerEventDetails($this->eventName, $eventDetails);

        $response = $this->viewRenderer->renderModel($modelOrClass, $setupModelCallback);

        $this->eventDispatcher->unregisterEventDetails($this->eventName, $eventDetails);

        return $response;
    }
}
