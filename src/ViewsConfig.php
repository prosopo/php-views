<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

class ViewsConfig
{
    private string $templatesRootPath;
    private string $viewsRootNamespace;
    private string $templateFileExtension;
    private TemplateRendererInterface $templateRenderer;

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
}
