<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Closure;
use Exception;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\View\ViewNamespaceManagerInterface;
use Prosopo\Views\Interfaces\View\ViewNamespaceModulesContainerInterface;
use Prosopo\Views\PrivateClasses\Model\ModelNamespaceProvider;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;
use Prosopo\Views\PrivateClasses\View\ViewNamespace;
use Prosopo\Views\PrivateClasses\View\ViewNamespaceModulesContainer;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewNamespaceModules;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class Views implements ViewNamespaceManagerInterface, ModelFactoryInterface, ModelRendererInterface
{
    private string $namespaceNotFoundErrorMessage;
    private string $wrongModelErrorMessage;
    private ModelNamespaceProviderInterface $modelNamespaceProvider;
    private ViewNamespaceModulesContainerInterface $namespaceModulesContainer;

    public function __construct(?ViewsConfig $config = null)
    {
        $config = null === $config ?
            new ViewsConfig() :
            $config;

        $modelNamespaceProvider = $config->getModelNamespaceProvider();
        $this->modelNamespaceProvider = null === $modelNamespaceProvider ?
            new ModelNamespaceProvider(new ObjectClassReader()) :
            $modelNamespaceProvider;

        $namespaceModulesContainer = $config->getNamespaceModulesContainer();
        $this->namespaceModulesContainer = null === $namespaceModulesContainer ?
            new ViewNamespaceModulesContainer() :
            $namespaceModulesContainer;

        $this->namespaceNotFoundErrorMessage = $config->getNamespaceNotFoundErrorMessage();
        $this->wrongModelErrorMessage = $config->getWrongModelErrorMessage();
    }

    public function addNamespace(string $namespace, ViewNamespaceConfig $config): ViewNamespaceModules
    {
        $viewNamespace = $this->makeViewNamespace($namespace, $config);

        $viewNamespaceModules = $viewNamespace->getModules();

        $this->namespaceModulesContainer->addNamespaceModules($namespace, $viewNamespaceModules);

        return $viewNamespaceModules;
    }

    public function makeModel(string $modelClass)
    {
        if (false === $this->isModel($modelClass)) {
            throw $this->makeWrongModelException($modelClass);
        }

        $modelNamespace = $this->modelNamespaceProvider->getModelNamespace($modelClass);
        $namespaceModules = $this->namespaceModulesContainer->getNamespaceModulesByModelNamespace($modelNamespace);

        if (null === $namespaceModules) {
            throw $this->makeNamespaceNotResolvedException($modelNamespace);
        }

        $modelFactory = $namespaceModules->getModelFactory();

        if (null === $modelFactory) {
            throw $this->makeNamespaceNotResolvedException($modelNamespace);
        }

        return $modelFactory->makeModel($modelClass);
    }

    public function renderModel($modelOrClass, Closure $setupCallback = null, bool $doPrint = false): string
    {
        if (false === $this->isModel($modelOrClass)) {
            throw $this->makeWrongModelException($modelOrClass);
        }

        $modelNamespace = $this->modelNamespaceProvider->getModelNamespace($modelOrClass);
        $namespaceModules = $this->namespaceModulesContainer->getNamespaceModulesByModelNamespace($modelNamespace);

        if (null === $namespaceModules) {
            throw $this->makeNamespaceNotResolvedException($modelNamespace);
        }

        $modelRenderer = $namespaceModules->getModelRenderer();

        if (null === $modelRenderer) {
            throw $this->makeNamespaceNotResolvedException($modelNamespace);
        }

        return $modelRenderer->renderModel($modelOrClass, $setupCallback, $doPrint);
    }

    protected function makeViewNamespace(string $namespace, ViewNamespaceConfig $config): ViewNamespace
    {
        return new ViewNamespace($namespace, $config, $this, $this);
    }

    protected function makeNamespaceNotResolvedException(string $namespace): Exception
    {
        $message = sprintf('%s : %s', $this->namespaceNotFoundErrorMessage, $namespace);

        return new Exception($message);
    }
    /**
     * @param string|object $modelOrClass
     */
    protected function makeWrongModelException($modelOrClass): Exception
    {
        $modelClass = true === is_object($modelOrClass) ?
            get_class($modelOrClass) :
            $modelOrClass;

        $message = sprintf('%s : %s', $this->wrongModelErrorMessage, $modelClass);

        return new Exception($message);
    }

    /**
     * @param string|object $modelOrClass
     */
    protected function isModel($modelOrClass): bool
    {
        if (true === is_object($modelOrClass)) {
            return $modelOrClass instanceof TemplateModelInterface;
        }

        if (false === class_exists($modelOrClass)) {
            return false;
        }

        $implementedList = class_implements($modelOrClass);

        return true === in_array(TemplateModelInterface::class, $implementedList, true);
    }
}
