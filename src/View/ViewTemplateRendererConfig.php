<?php

declare(strict_types=1);

namespace Prosopo\Views\View;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class ViewTemplateRendererConfig
{
    private bool $fileBasedTemplates;
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

    private ViewTemplateRendererModules $modules;

    public function __construct()
    {
        $this->fileBasedTemplates = true;
        $this->escapeVariableName = 'escape';
        $this->templateErrorEventName = 'template_error';
        $this->globalVariables = [];

        $this->templateErrorHandler = null;
        $this->customOutputEscapeCallback = null;
        $this->compilerExtensionCallback = null;

        $this->modules = new ViewTemplateRendererModules();
    }

    //// Getters:

    public function fileBasedTemplates(): bool
    {
        return $this->fileBasedTemplates;
    }

    /**
     * @return  callable(array<string,mixed> $eventDetails):void|null
     */
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

    /**
     * @return  callable(mixed $variable): string|null
     */
    public function getCustomOutputEscapeCallback(): ?callable
    {
        return $this->customOutputEscapeCallback;
    }

    public function getEscapeVariableName(): string
    {
        return $this->escapeVariableName;
    }

    /**
     * @return callable(string $template): string|null
     */
    public function getCompilerExtensionCallback(): ?callable
    {
        return $this->compilerExtensionCallback;
    }

    public function getTemplateErrorEventName(): string
    {
        return $this->templateErrorEventName;
    }

    public function getModules(): ViewTemplateRendererModules
    {
        return $this->modules;
    }

    //// Setters:

    public function setFileBasedTemplates(bool $fileBasedTemplates): self
    {
        $this->fileBasedTemplates = $fileBasedTemplates;

        return $this;
    }

    /**
     * @param callable(array<string,mixed> $eventDetails):void|null $templateErrorHandler
     */
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

    /**
     * @param callable(string $template): string|null $compilerExtensionCallback
     */
    public function setCompilerExtensionCallback(?callable $compilerExtensionCallback): self
    {
        $this->compilerExtensionCallback = $compilerExtensionCallback;

        return $this;
    }

    /**
     * @param callable(mixed $variable): string|null $customOutputEscapeCallback
     */
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

    public function setModules(ViewTemplateRendererModules $modules): self
    {
        $this->modules = $modules;

        return  $this;
    }
}
