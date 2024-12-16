<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class FileTemplateProvider implements TemplateProviderInterface
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

    public function getTemplate(TemplateModelInterface $model): string
    {
        $modelClass = $this->getModelClass($model);

        $relativeTemplatePath = $this->getRelativeTemplatePath($modelClass, $this->viewsRootNamespace);

        $absoluteTemplatePath = $this->getAbsoluteTemplatePath($relativeTemplatePath);

        return $this->getFileContent($absoluteTemplatePath);
    }

    protected function getModelClass(TemplateModelInterface $model): string
    {
        return get_class($model);
    }

    protected function getFileContent(string $file): string
    {
        if (false === file_exists($file)) {
            return '';
        }

        // @phpcs:ignore
        return (string)file_get_contents($file);
    }

    protected function getAbsoluteTemplatePath(string $relativeTemplatePath): string
    {
        return $this->templatesRootPath . DIRECTORY_SEPARATOR . $relativeTemplatePath . $this->extension;
    }

    protected function getRelativeTemplatePath(string $modelClass, string $rootNamespace): string
    {
        $relativeNamespace = str_replace($rootNamespace, '', $modelClass);
        $relativeNamespace = ltrim($relativeNamespace, '\\');

        $shortClassName = str_replace('\\', DIRECTORY_SEPARATOR, $relativeNamespace);

        $dashedShortName = (string)preg_replace('/([a-z])([A-Z])/', '$1-$2', $shortClassName);

        return strtolower($dashedShortName);
    }
}
