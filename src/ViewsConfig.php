<?php

declare(strict_types=1);

namespace Prosopo\Views;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods and properties, and introduce new public ones.
 *
 * We opt to use a class instead of an interface because it allows for the addition of new (optional) settings,
 * without breaking existing setups.
 */
final class ViewsConfig
{
    //// Required settings:

    private string $templatesRootPath;
    private string $viewsRootNamespace;
    private string $templateFileExtension;
    private ModulesCollection $modulesCollection;

    //// Optional settings:

    /**
     * @var array<string,mixed>
     */
    private array $defaultPropertyValues;

    public function __construct()
    {
        // Defaults are not set for required modules.
        // This is intentional to ensure an Exception is thrown if their getters are called without providing values.

        $this->defaultPropertyValues = array(
            'array'  => array(),
            'bool'   => false,
            'float'  => 0.0,
            'int'    => 0,
            'string' => '',
        );
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

    public function setModulesCollection(ModulesCollection $modulesCollection): void
    {
        $this->modulesCollection = $modulesCollection;
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

    /**
     * @param array<string,mixed> $defaultPropertyValues
     */
    public function setDefaultPropertyValues(array $defaultPropertyValues): void
    {
        $this->defaultPropertyValues = $defaultPropertyValues;
    }

    public function getModulesCollection(): ModulesCollection
    {
        return $this->modulesCollection;
    }
}
