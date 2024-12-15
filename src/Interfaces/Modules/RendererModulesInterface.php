<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Modules;

use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

interface RendererModulesInterface
{
    //// Getters.

    public function getEventDispatcher(): ?EventDispatcherInterface;

    public function getTemplateRenderer(): ?TemplateRendererInterface;

    public function getTemplateCompiler(): ?TemplateCompilerInterface;

    public function getCodeExecutor(): ?CodeExecutorInterface;

    //// Setters.

    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): self;

    public function setTemplateRenderer(?TemplateRendererInterface $templateRenderer): self;

    public function setTemplateCompiler(?TemplateCompilerInterface $templateCompiler): self;

    public function setCodeExecutor(?CodeExecutorInterface $codeExecutor): self;
}
