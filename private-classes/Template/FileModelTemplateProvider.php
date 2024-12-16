<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateProviderInterface;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class FileModelTemplateProvider implements ModelTemplateProviderInterface
{
    private string $templatesRootPath;
    private string $viewsRootNamespace;
    private string $extension;
    private ModelNamespaceProviderInterface $modelNamespaceProvider;

    public function __construct(
        string $templatesRootPath,
        string $viewsRootNamespace,
        string $extension,
        ModelNamespaceProviderInterface $modelNamespaceProvider
    ) {
        $this->templatesRootPath = $templatesRootPath;
        $this->viewsRootNamespace = $viewsRootNamespace;
        $this->extension = $extension;
        $this->modelNamespaceProvider = $modelNamespaceProvider;
    }

    public function getModelTemplate(TemplateModelInterface $model): string
    {
        $modelNamespace = $this->modelNamespaceProvider->getModelNamespace($model);

        $relativeTemplatePath = $this->getRelativeTemplatePath($modelNamespace, $this->viewsRootNamespace);

        $absoluteTemplatePath = $this->getAbsoluteTemplatePath($relativeTemplatePath);

        return $this->getFileContent($absoluteTemplatePath);
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

    // fixme
    protected function getRelativeTemplatePath(string $modelNamespace, string $rootNamespace): string
    {
        $shortClassName = str_replace('\\', DIRECTORY_SEPARATOR, $relativeNamespace);

        $dashedShortName = (string)preg_replace('/([a-z])([A-Z])/', '$1-$2', $shortClassName);

        return strtolower($dashedShortName);
    }
}
