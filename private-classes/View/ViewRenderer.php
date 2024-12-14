<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Closure;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewRenderer implements ViewRendererInterface
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
        $view = true === is_string($viewOrClass) ?
            $this->viewFactory->makeView($viewOrClass) :
            $viewOrClass;

        if (null !== $setupCallback) {
            $setupCallback($view);
        }

        $template  = $view->getTemplate();
        $variables = $this->objectPropertyManager->getVariables($view);

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
