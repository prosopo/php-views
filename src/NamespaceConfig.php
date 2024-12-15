<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\Config\NamespaceConfigInterface;
use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Modules;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class NamespaceConfig implements NamespaceConfigInterface
{
    //// Required settings:

    private string $templatesRootPath;
    private string $viewsRootNamespace;
    private string $templateFileExtension;

    //// Optional settings:

    /**
     * @var callable(array<string,mixed> $eventDetails): void|null
     */
    private $templateErrorHandler;
    private string $templateErrorEventName;
    /**
     * @var array<string,mixed>
     */
    private array $defaultPropertyValues;

    //// Own properties:

    private ModulesInterface $modules;

    public function __construct(TemplateRendererInterface $templateRenderer)
    {
        // Defaults are not set for required modules.
        // This is intentional to ensure an Exception is thrown if their getters are called without providing values.

        $this->modules = new Modules($templateRenderer);

        $this->templateErrorHandler = null;
        $this->defaultPropertyValues = array(
            'array'  => array(),
            'bool'   => false,
            'float'  => 0.0,
            'int'    => 0,
            'string' => '',
        );
        $this->templateErrorEventName = 'template_error';
    }

    //// Getters.

    public function getTemplatesRootPath(): string
    {
        return $this->templatesRootPath;
    }

    public function getViewsRootNamespace(): string
    {
        return $this->viewsRootNamespace;
    }

    public function getTemplateFileExtension(): string
    {
        return $this->templateFileExtension;
    }

    public function getTemplateErrorHandler(): ?callable
    {
        return $this->templateErrorHandler;
    }

    public function getTemplateErrorEventName(): string
    {
        return $this->templateErrorEventName;
    }

    public function getDefaultPropertyValues(): array
    {
        return $this->defaultPropertyValues;
    }

    public function getModules(): ModulesInterface
    {
        return $this->modules;
    }

    //// Setters:

    public function setTemplatesRootPath(string $templatesRootPath): self
    {
        $this->templatesRootPath = $templatesRootPath;

        return $this;
    }

    public function setViewsRootNamespace(string $viewsRootNamespace): self
    {
        $this->viewsRootNamespace = $viewsRootNamespace;

        return $this;
    }

    public function setTemplateFileExtension(string $templateFileExtension): self
    {
        $this->templateFileExtension = $templateFileExtension;

        return $this;
    }

    public function setTemplateErrorHandler(?callable $templateErrorHandler): self
    {
        $this->templateErrorHandler = $templateErrorHandler;

        return $this;
    }

    public function setDefaultPropertyValues(array $defaultPropertyValues): self
    {
        $this->defaultPropertyValues = $defaultPropertyValues;

        return $this;
    }

    public function setTemplateErrorEventName(string $templateErrorEventName): self
    {
        $this->templateErrorEventName = $templateErrorEventName;

        return $this;
    }
}
