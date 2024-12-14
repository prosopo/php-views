<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateErrorDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods and properties, and introduce new public ones.
 *
 * We opt to use a class instead of an interface because it allows for the addition of new (optional) settings,
 *  without breaking existing setups.
 */
final class BladeRendererModules
{
    //// Custom modules: define them only when you need to override the default behavior:

    private ?TemplateErrorDispatcherInterface $templateErrorDispatcher;
    private ?TemplateRendererInterface $templateRenderer;
    private ?TemplateCompilerInterface $templateCompiler;
    private ?TemplateRendererInterface $templateRendererWithCustomEscape;

    public function __construct()
    {
        $this->templateErrorDispatcher = null;
        $this->templateRenderer = null;
        $this->templateCompiler = null;
        $this->templateRendererWithCustomEscape = null;
    }

    //// Getters.

    public function getTemplateErrorDispatcher(): ?TemplateErrorDispatcherInterface
    {
        return $this->templateErrorDispatcher;
    }

    public function getTemplateRenderer(): ?TemplateRendererInterface
    {
        return $this->templateRenderer;
    }

    public function getTemplateCompiler(): ?TemplateCompilerInterface
    {
        return $this->templateCompiler;
    }

    public function getTemplateRendererWithCustomEscape(): ?TemplateRendererInterface
    {
        return $this->templateRendererWithCustomEscape;
    }

    //// Setters.

    public function setTemplateErrorDispatcher(?TemplateErrorDispatcherInterface $templateErrorDispatcher): self
    {
        $this->templateErrorDispatcher = $templateErrorDispatcher;

        return $this;
    }

    public function setTemplateRenderer(?TemplateRendererInterface $templateRenderer): self
    {
        $this->templateRenderer = $templateRenderer;

        return $this;
    }

    public function setTemplateCompiler(?TemplateCompilerInterface $templateCompiler): self
    {
        $this->templateCompiler = $templateCompiler;

        return $this;
    }

    public function setTemplateRendererWithCustomEscape(
        ?TemplateRendererInterface $templateRendererWithCustomEscape
    ): self {
        $this->templateRendererWithCustomEscape = $templateRendererWithCustomEscape;

        return $this;
    }
}
