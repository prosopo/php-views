<?php

declare(strict_types=1);

namespace Prosopo\Views\Blade;

use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateErrorDispatcherInterface;
use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

class BladeRendererConfig
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

    //// Custom modules (set them when you need to override the default classes):

    private ?TemplateErrorDispatcherInterface $templateErrorDispatcher;
    private ?TemplateRendererInterface $templateRenderer;
    private ?TemplateCompilerInterface $templateCompiler;
    private ?TemplateRendererInterface $templateRendererWithCustomEscape;

    public function __construct()
    {
        $this->templateErrorHandler = null;
        $this->customOutputEscapeCallback = null;
        $this->globalVariables = [];
        $this->escapeVariableName = 'escape';
        $this->compilerExtensionCallback = null;
        $this->templateErrorDispatcher = null;
        $this->templateRenderer = null;
        $this->templateCompiler = null;
        $this->templateRendererWithCustomEscape = null;
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

    public function setTemplateErrorDispatcher(?TemplateErrorDispatcherInterface $templateErrorDispatcher): void
    {
        $this->templateErrorDispatcher = $templateErrorDispatcher;
    }

    public function setTemplateRenderer(?TemplateRendererInterface $templateRenderer): void
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function setTemplateCompiler(?TemplateCompilerInterface $templateCompiler): void
    {
        $this->templateCompiler = $templateCompiler;
    }

    public function setTemplateRendererWithCustomEscape(
        ?TemplateRendererInterface $templateRendererWithCustomEscape
    ): void {
        $this->templateRendererWithCustomEscape = $templateRendererWithCustomEscape;
    }
}
