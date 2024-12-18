<?php

declare(strict_types=1);

namespace Prosopo\Views\View;

use Prosopo\Views\Interfaces\CodeRunnerInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties, and introduce new public ones.
 */
final class ViewTemplateRendererModules
{
    //// Custom modules: define them only when you need to override the default behavior:

    private ?TemplateRendererInterface $templateRenderer;
    private ?EventDispatcherInterface $eventDispatcher;
    private ?TemplateCompilerInterface $templateCompiler;
    private ?CodeRunnerInterface $phpCodeExecutor;

    public function __construct()
    {
        $this->templateRenderer = null;
        $this->eventDispatcher = null;
        $this->templateCompiler = null;
        $this->phpCodeExecutor = null;
    }

    //// Getters.

    public function getTemplateRenderer(): ?TemplateRendererInterface
    {
        return $this->templateRenderer;
    }

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getTemplateCompiler(): ?TemplateCompilerInterface
    {
        return $this->templateCompiler;
    }

    public function getCodeExecutor(): ?CodeRunnerInterface
    {
        return $this->phpCodeExecutor;
    }

    //// Setters.

    public function setTemplateRenderer(?TemplateRendererInterface $templateRenderer): self
    {
        $this->templateRenderer = $templateRenderer;

        return $this;
    }

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    public function setTemplateCompiler(?TemplateCompilerInterface $templateCompiler): self
    {
        $this->templateCompiler = $templateCompiler;

        return $this;
    }

    public function setCodeExecutor(?CodeRunnerInterface $codeExecutor): self
    {
        $this->phpCodeExecutor = $codeExecutor;

        return $this;
    }
}
