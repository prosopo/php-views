<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Closure;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyReaderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ViewRenderer implements ViewRendererInterface
{
    private TemplateRendererInterface $templateRenderer;
    private ViewFactoryInterface $viewFactory;
    private ObjectPropertyReaderInterface $objectPropertyReader;

    public function __construct(
        TemplateRendererInterface $templateRenderer,
        ViewFactoryInterface $viewFactory,
        ObjectPropertyReaderInterface $objectPropertyReader
    ) {
        $this->templateRenderer       = $templateRenderer;
        $this->viewFactory            = $viewFactory;
        $this->objectPropertyReader = $objectPropertyReader;
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
        $variables = $this->objectPropertyReader->getVariables($view);

        return $this->templateRenderer->renderTemplate($template, $variables, $doPrint);
    }
}
