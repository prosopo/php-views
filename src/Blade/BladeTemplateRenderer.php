<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Template\TemplateErrorDispatcher;
use Prosopo\Views\Template\TemplateRenderer;
use Prosopo\Views\Template\TemplateRendererWithCustomEscape;

class BladeTemplateRenderer implements TemplateRendererInterface
{
    private TemplateRendererInterface $templateRenderer;

    public function __construct(BladeRendererConfig $config)
    {
        $templateErrorDispatcher = new TemplateErrorDispatcher($config->getTemplateErrorHandler());

        $templateRenderer = new TemplateRenderer(
            new BladeCompiler($config->getEscapeVariableName(), $config->getCompilerExtensionCallback()),
            $templateErrorDispatcher,
            $config->getGlobalVariables()
        );

        $this->templateRenderer = new TemplateRendererWithCustomEscape(
            $templateRenderer,
            $config->getCustomOutputEscapeCallback(),
            $config->getEscapeVariableName()
        );
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string
    {
        return $this->templateRenderer->renderTemplate($template, $variables, $doPrint);
    }
}
