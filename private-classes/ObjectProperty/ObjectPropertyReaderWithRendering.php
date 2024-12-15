<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\ObjectProperty;

use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyReaderInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;

final class ObjectPropertyReaderWithRendering implements ObjectPropertyReaderInterface
{
    private ObjectPropertyReaderInterface $objectPropertyReader;
    private ViewRendererInterface $viewRenderer;

    public function __construct(
        ObjectPropertyReaderInterface $objectPropertyReader,
        ViewRendererInterface $viewRenderer
    ) {
        $this->objectPropertyReader = $objectPropertyReader;
        $this->viewRenderer = $viewRenderer;
    }

    public function getVariables(object $instance): array
    {
        $variables = $this->objectPropertyReader->getVariables($instance);

        return $this->renderInnerViews($variables);
    }

    /**
     * @param array<string,mixed> $variables
     *
     * @return array<string,mixed>
     */
    protected function renderInnerViews(array $variables): array
    {
        return array_map(
            function ($item) {
                return $this->renderIfView($item);
            },
            $variables
        );
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    protected function renderIfView($item)
    {
        if (true === ( $item instanceof ViewInterface )) {
            $item = $this->viewRenderer->renderView($item);
        } elseif (
            true === is_array($item) &&
            false === is_callable($item)
        ) {
            // @phpstan-ignore-next-line
            $item = $this->renderInnerViews($item);
        }

        return $item;
    }
}
