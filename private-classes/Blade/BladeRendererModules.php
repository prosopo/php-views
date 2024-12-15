<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Blade;

use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Modules\RendererModulesInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class BladeRendererModules implements RendererModulesInterface
{
    //// Custom modules: define them only when you need to override the default behavior:

    private ?EventDispatcherInterface $eventDispatcher;
    private ?TemplateCompilerInterface $templateCompiler;
    private ?TemplateRendererInterface $templateRenderer;
    private ?CodeExecutorInterface $phpCodeExecutor;

    public function __construct()
    {
        $this->eventDispatcher = null;
        $this->templateRenderer = null;
        $this->templateCompiler = null;
        $this->phpCodeExecutor = null;
    }

    //// Getters.

    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getTemplateRenderer(): ?TemplateRendererInterface
    {
        return $this->templateRenderer;
    }

    public function getTemplateCompiler(): ?TemplateCompilerInterface
    {
        return $this->templateCompiler;
    }

    public function getCodeExecutor(): ?CodeExecutorInterface
    {
        return $this->phpCodeExecutor;
    }

    //// Setters.

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;

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

    public function setCodeExecutor(?CodeExecutorInterface $codeExecutor): self
    {
        $this->phpCodeExecutor = $codeExecutor;

        return $this;
    }
}
