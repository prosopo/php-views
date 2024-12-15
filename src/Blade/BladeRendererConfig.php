<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

use Prosopo\Views\Interfaces\Config\BladeRendererConfigInterface;
use Prosopo\Views\Interfaces\Modules\RendererModulesInterface;
use Prosopo\Views\PrivateClasses\Blade\BladeRendererModules;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class BladeRendererConfig implements BladeRendererConfigInterface
{
    //// Optional settings:

    /**
     * @var callable(array<string,mixed> $eventDetails):void|null
     */
    private $templateErrorHandler;
    /**
     * @var callable(mixed $variable): string|null
     */
    private $customOutputEscapeCallback;
    /**
     * @var array<string,mixed>
     */
    private array $globalVariables;

    /**
     * @var callable(string $template): string|null
     */
    private $compilerExtensionCallback;
    private string $escapeVariableName;
    private string $templateErrorEventName;

    //// Own properties:

    private RendererModulesInterface $modules;

    public function __construct()
    {
        $this->templateErrorHandler = null;
        $this->customOutputEscapeCallback = null;
        $this->globalVariables = [];
        $this->compilerExtensionCallback = null;
        $this->templateErrorEventName = 'template_error';
        $this->escapeVariableName = 'escape';

        $this->modules = new BladeRendererModules();
    }

    //// Getters:

    public function getTemplateErrorHandler(): ?callable
    {
        return $this->templateErrorHandler;
    }

    public function getGlobalVariables(): array
    {
        return $this->globalVariables;
    }

    public function getCustomOutputEscapeCallback(): ?callable
    {
        return $this->customOutputEscapeCallback;
    }

    public function getEscapeVariableName(): string
    {
        return $this->escapeVariableName;
    }

    public function getCompilerExtensionCallback(): ?callable
    {
        return $this->compilerExtensionCallback;
    }

    public function getTemplateErrorEventName(): string
    {
        return $this->templateErrorEventName;
    }

    public function getModules(): RendererModulesInterface
    {
        return $this->modules;
    }

    //// Setters:

    public function setTemplateErrorHandler(?callable $templateErrorHandler): self
    {
        $this->templateErrorHandler = $templateErrorHandler;

        return $this;
    }

    public function setGlobalVariables(array $globalVariables): self
    {
        $this->globalVariables = $globalVariables;

        return $this;
    }

    public function setCompilerExtensionCallback(?callable $compilerExtensionCallback): self
    {
        $this->compilerExtensionCallback = $compilerExtensionCallback;

        return $this;
    }

    public function setCustomOutputEscapeCallback(?callable $customOutputEscapeCallback): self
    {
        $this->customOutputEscapeCallback = $customOutputEscapeCallback;

        return $this;
    }

    public function setEscapeVariableName(string $escapeVariableName): self
    {
        $this->escapeVariableName = $escapeVariableName;

        return $this;
    }

    public function setTemplateErrorEventName(string $templateErrorEventName): self
    {
        $this->templateErrorEventName = $templateErrorEventName;

        return $this;
    }
}
