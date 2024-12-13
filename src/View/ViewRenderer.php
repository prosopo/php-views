<?php

declare(strict_types=1);

namespace Prosopo\Views\View;

use Closure;
use Prosopo\Views\Interfaces\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;

class ViewRenderer implements ViewRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    private ViewFactoryInterface $viewFactory;
    private ObjectPropertyManagerInterface $objectPropertyManager;

    public function __construct(
        TemplateRendererInterface $templateRenderer,
        ViewFactoryInterface $viewFactory,
        ObjectPropertyManagerInterface $objectPropertyManager
    ) {
        $this->templateRenderer       = $templateRenderer;
        $this->viewFactory            = $viewFactory;
        $this->objectPropertyManager = $objectPropertyManager;
    }

    public function renderView($viewOrClass, ?Closure $setupCallback = null, bool $doPrint = false): string
    {
        $component = true === is_string($viewOrClass) ?
            $this->viewFactory->makeView($viewOrClass, $setupCallback) :
            $viewOrClass;

        $template  = $component->getTemplate();
        $variables = $this->objectPropertyManager->getVariables($component);

        $variables = $this->renderNestedViews($variables);

        return $this->templateRenderer->renderTemplate($template, $variables, $doPrint);
    }

    /**
     * @param array<string,mixed> $variables
     *
     * @return array<string,mixed>
     */
    protected function renderNestedViews(array $variables): array
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
            $item = $this->renderView($item);
        } elseif (
            true === is_array($item) &&
                    false === is_callable($item)
        ) {
            // @phpstan-ignore-next-line
            $item = $this->renderNestedViews($item);
        }

        return $item;
    }
}
