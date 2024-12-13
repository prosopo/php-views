<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;

class ViewsConfig
{
    //// Required settings:

    private string $templatesRootPath;
    private string $viewsRootNamespace;
    private string $templateFileExtension;
    private TemplateRendererInterface $templateRenderer;

    //// Custom modules (set them when you need to override the default classes):

    private ?ViewFactoryInterface $viewFactory;
    private ?TemplateProviderInterface $templateProvider;
    private ?ObjectPropertyManagerInterface $objectPropertyManager;
    private ?ViewRendererInterface $viewRenderer;

    public function __construct()
    {
        // We initialize only the optional properties,
        // so getters will throw an Exception if the required fields aren't filled.
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
}
