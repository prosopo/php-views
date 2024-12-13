<?php

declare(strict_types=1);

namespace Prosopo\Views\Template;

use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;

class TemplateProvider implements TemplateProviderInterface
{
    private string $templatesRootPath;
    private string $viewsRootNamespace;
    private string $extension;

    public function __construct(string $templatesRootPath, string $viewsRootNamespace, string $extension)
    {
        $this->templatesRootPath = $templatesRootPath;
        $this->viewsRootNamespace = $viewsRootNamespace;
        $this->extension = $extension;
    }

    public function getTemplate(ViewInterface $view): string
    {
        $viewFileName = $this->getRelativeViewPath(get_class($view), $this->viewsRootNamespace);

        $pathToView = $this->getAbsoluteViewPath($viewFileName);

        return $this->getFileContentSafely($pathToView);
    }

    protected function getFileContentSafely(string $file): string
    {
        if (false === file_exists($file)) {
            return '';
        }

        // @phpcs:ignore
        return (string)file_get_contents($file);
    }

    protected function getAbsoluteViewPath(string $viewName): string
    {
        return $this->templatesRootPath . DIRECTORY_SEPARATOR . $viewName . $this->extension;
    }

    protected function getRelativeViewPath(string $viewClass, string $rootNamespace): string
    {
        $relativeNamespace = str_replace($rootNamespace, '', $viewClass);
        $relativeNamespace = ltrim($relativeNamespace, '\\');

        $shortClassName = str_replace('\\', DIRECTORY_SEPARATOR, $relativeNamespace);

        $dashedShortName = (string)preg_replace('/([a-z])([A-Z])/', '$1-$2', $shortClassName);

        return strtolower($dashedShortName);
    }
}
