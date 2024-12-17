<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Closure;
use Exception;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
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
final class View implements ViewNamespaceManagerInterface, ModelFactoryInterface, ModelRendererInterface
{
    private string $namespaceNotFoundErrorMessage;
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

    protected function makeNamespaceNotResolvedException(string $modelNamespace): Exception
    {
        $message = sprintf('%s : %s', $this->namespaceNotFoundErrorMessage, $modelNamespace);

        return new Exception($message);
    }
}
