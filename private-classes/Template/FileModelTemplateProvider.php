<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\Model\ModelNameProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateProviderInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class FileModelTemplateProvider implements ModelTemplateProviderInterface
{
    private string $templatesRootPath;
    private string $viewsRootNamespace;
    private string $extension;
    private ModelNameProviderInterface $modelNameProvider;
    private ModelNamespaceProviderInterface $modelNamespaceProvider;

    public function __construct(
        string $templatesRootPath,
        string $viewsRootNamespace,
        string $extension,
        ModelNamespaceProviderInterface $modelNamespaceProvider,
        ModelNameProviderInterface $modelNameProvider
    ) {
        $this->templatesRootPath = $templatesRootPath;
        $this->viewsRootNamespace = $viewsRootNamespace;
        $this->extension = $extension;
        $this->modelNameProvider = $modelNameProvider;
        $this->modelNamespaceProvider = $modelNamespaceProvider;
    }

    public function getModelTemplate(TemplateModelInterface $model): string
    {
        $modelNamespace = $this->modelNamespaceProvider->getModelNamespace($model);
        $relativeModelNamespace = substr($modelNamespace, strlen($this->viewsRootNamespace));
        $modelName = $this->modelNameProvider->getModelName($model);

        $relativeTemplatePath = $this->getRelativeTemplatePath($relativeModelNamespace, $modelName);

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

    protected function getRelativeTemplatePath(string $relativeModelNamespace, string $modelName): string
    {
        $relativeModelPath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeModelNamespace);
        $modelName = (string)preg_replace('/([a-z])([A-Z])/', '$1-$2', $modelName);

        $relativeTemplatePath = $relativeModelPath . DIRECTORY_SEPARATOR . $modelName;

        return strtolower($relativeTemplatePath);
    }
}
