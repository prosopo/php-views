<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods and properties, or introduce new ones.
 *
 * We chose to use a class instead of an interface because it allows for the addition of new (optional) settings,
 * without breaking existing implementations.
 */
final class ViewsConfig
{
    //// Required settings:

    private string $templatesRootPath;
    private string $viewsRootNamespace;
    private string $templateFileExtension;
    private TemplateRendererInterface $templateRenderer;

    //// Optional settings:

    /**
     * @var array<string,mixed>
     */
    private array $defaultPropertyValues;

    //// Custom modules (set them when you need to override the default classes):

    private ?ViewFactoryInterface $viewFactory;
    private ?TemplateProviderInterface $templateProvider;
    private ?ObjectPropertyManagerInterface $objectPropertyManager;
    private ?ViewRendererInterface $viewRenderer;

    public function __construct()
    {
        // We initialize only the optional properties,
        // so getters will throw an Exception if the required fields aren't filled.

        $this->defaultPropertyValues = array(
            'array'  => array(),
            'bool'   => false,
            'float'  => 0.0,
            'int'    => 0,
            'object' => null,
            'string' => '',
        );

        $this->viewFactory = null;
        $this->templateProvider = null;
        $this->objectPropertyManager = null;
        $this->viewRenderer = null;
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

    public function getTemplateRenderer(): TemplateRendererInterface
    {
        return $this->templateRenderer;
    }

    public function getViewFactory(): ?ViewFactoryInterface
    {
        return $this->viewFactory;
    }

    public function getTemplateProvider(): ?TemplateProviderInterface
    {
        return $this->templateProvider;
    }

    public function getObjectPropertyManager(): ?ObjectPropertyManagerInterface
    {
        return $this->objectPropertyManager;
    }

    public function getViewRenderer(): ?ViewRendererInterface
    {
        return $this->viewRenderer;
    }

    /**
     * @return array<string,mixed>
     */
    public function getDefaultPropertyValues(): array
    {
        return $this->defaultPropertyValues;
    }

    //// Setters.

    public function setTemplatesRootPath(string $templatesRootPath): void
    {
        $this->templatesRootPath = $templatesRootPath;
    }

    public function setViewsRootNamespace(string $viewsRootNamespace): void
    {
        $this->viewsRootNamespace = $viewsRootNamespace;
    }

    public function setTemplateFileExtension(string $templateFileExtension): void
    {
        $this->templateFileExtension = $templateFileExtension;
    }

    public function setTemplateRenderer(TemplateRendererInterface $templateRenderer): void
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function setViewFactory(?ViewFactoryInterface $viewFactory): void
    {
        $this->viewFactory = $viewFactory;
    }

    public function setTemplateProvider(?TemplateProviderInterface $templateProvider): void
    {
        $this->templateProvider = $templateProvider;
    }

    public function setObjectPropertyManager(?ObjectPropertyManagerInterface $objectPropertyManager): void
    {
        $this->objectPropertyManager = $objectPropertyManager;
    }

    public function setViewRenderer(?ViewRendererInterface $viewRenderer): void
    {
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * @param array<string,mixed> $defaultPropertyValues
     */
    public function setDefaultPropertyValues(array $defaultPropertyValues): void
    {
        $this->defaultPropertyValues = $defaultPropertyValues;
    }
}
