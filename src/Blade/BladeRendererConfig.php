<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;

class BladeRendererConfig
{
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

    public function __construct()
    {
        $this->templateErrorHandler = null;
        $this->customOutputEscapeCallback = null;
        $this->globalVariables = [];
        $this->escapeVariableName = 'escape';
        $this->compilerExtensionCallback = null;
    }

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

    /**
     * @param callable(string $template): string|null $compilerExtensionCallback
     */
    public function setCompilerExtensionCallback(?callable $compilerExtensionCallback): void
    {
        $this->compilerExtensionCallback = $compilerExtensionCallback;
    }

    /**
     * @param callable(TemplateErrorInterface $templateError): void|null $templateErrorHandler
     */
    public function setTemplateErrorHandler(?callable $templateErrorHandler): void
    {
        $this->templateErrorHandler = $templateErrorHandler;
    }

    public function setCustomOutputEscapeCallback(?callable $customOutputEscapeCallback): void
    {
        $this->customOutputEscapeCallback = $customOutputEscapeCallback;
    }

    /**
     * @param array<string,mixed> $globalVariables
     */
    public function setGlobalVariables(array $globalVariables): void
    {
        $this->globalVariables = $globalVariables;
    }

    public function setEscapeVariableName(string $escapeVariableName): void
    {
        $this->escapeVariableName = $escapeVariableName;
    }
}
