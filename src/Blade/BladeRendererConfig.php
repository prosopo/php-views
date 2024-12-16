<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class BladeRendererConfig
{
    private bool $isFileBasedTemplate;
    private string $escapeVariableName;
    private string $templateErrorEventName;
    /**
     * @var array<string,mixed>
     */
    private array $globalVariables;

    /**
     * @var callable(array<string,mixed> $eventDetails):void|null
     */
    private $templateErrorHandler;
    /**
     * @var callable(mixed $variable): string|null
     */
    private $customOutputEscapeCallback;
    /**
     * @var callable(string $template): string|null
     */
    private $compilerExtensionCallback;

    private BladeRendererModules $modules;

    public function __construct()
    {
        $this->isFileBasedTemplate = true;
        $this->escapeVariableName = 'escape';
        $this->templateErrorEventName = 'template_error';
        $this->globalVariables = [];

        $this->templateErrorHandler = null;
        $this->customOutputEscapeCallback = null;
        $this->compilerExtensionCallback = null;

        $this->modules = new BladeRendererModules();
    }

    //// Getters:

    public function isFileBasedTemplate(): bool
    {
        return $this->isFileBasedTemplate;
    }

    public function getTemplateErrorHandler(): ?callable
    {
        return $this->templateErrorHandler;
    }

    /**
     * @return array<string,mixed>
     */
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

    public function getModules(): BladeRendererModules
    {
        return $this->modules;
    }

    //// Setters:

    public function setIsFileBasedTemplate(bool $isFileBasedTemplate): self
    {
        $this->isFileBasedTemplate = $isFileBasedTemplate;

        return $this;
    }

    public function setTemplateErrorHandler(?callable $templateErrorHandler): self
    {
        $this->templateErrorHandler = $templateErrorHandler;

        return $this;
    }

    /**
     * @param array<string,mixed> $globalVariables
     */
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

    public function setModules(BladeRendererModules $modules): self
    {
        $this->modules = $modules;

        return  $this;
    }
}
