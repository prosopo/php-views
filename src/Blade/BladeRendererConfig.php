<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods and properties, and introduce new public ones.
 *
 * We opt to use a class instead of an interface because it allows for the addition of new (optional) settings,
 * without breaking existing setups.
 */
final class BladeRendererConfig
{
    //// Optional settings:

    /**
     * @var callable(TemplateErrorInterface $templateError): void|null
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
    private string $escapeVariableName;
    /**
     * @var callable(string $template): string|null
     */
    private $compilerExtensionCallback;
    private BladeRendererModules $modules;

    public function __construct()
    {
        $this->templateErrorHandler = null;
        $this->customOutputEscapeCallback = null;
        $this->globalVariables = [];
        $this->escapeVariableName = 'escape';
        $this->compilerExtensionCallback = null;
        $this->modules = new BladeRendererModules();
    }

    //// Getters.

    /**
     * @return callable(TemplateErrorInterface $templateError): void|null
     */
    public function getTemplateErrorHandler(): ?callable
    {
        return $this->templateErrorHandler;
    }

    /**
     * @return callable(mixed $variable): string|null
     */
    public function getCustomOutputEscapeCallback(): ?callable
    {
        return $this->customOutputEscapeCallback;
    }

    /**
     * @return array<string,mixed>
     */
    public function getGlobalVariables(): array
    {
        return $this->globalVariables;
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

    public function getModules(): BladeRendererModules
    {
        return $this->modules;
    }

    //// Setters.

    /**
     * @param callable(string $template): string|null $compilerExtensionCallback
     */
    public function setCompilerExtensionCallback(?callable $compilerExtensionCallback): self
    {
        $this->compilerExtensionCallback = $compilerExtensionCallback;

        return $this;
    }

    /**
     * @param callable(TemplateErrorInterface $templateError): void|null $templateErrorHandler
     */
    public function setTemplateErrorHandler(?callable $templateErrorHandler): self
    {
        $this->templateErrorHandler = $templateErrorHandler;

        return $this;
    }

    public function setCustomOutputEscapeCallback(?callable $customOutputEscapeCallback): self
    {
        $this->customOutputEscapeCallback = $customOutputEscapeCallback;

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

    public function setEscapeVariableName(string $escapeVariableName): self
    {
        $this->escapeVariableName = $escapeVariableName;

        return $this;
    }

    public function setModules(BladeRendererModules $modules): self
    {
        $this->modules = $modules;

        return $this;
    }
}
