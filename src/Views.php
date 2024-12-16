<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Closure;
use Exception;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\View\ViewNamespacesContainerInterface;
use Prosopo\Views\Interfaces\ViewsInterface;
use Prosopo\Views\PrivateClasses\Model\ModelNamespaceProvider;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;
use Prosopo\Views\PrivateClasses\View\ViewNamespace;
use Prosopo\Views\PrivateClasses\View\ViewNamespacesContainer;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class Views implements ViewsInterface, ModelFactoryInterface, ModelRendererInterface
{
    private string $namespaceNotFoundErrorMessage;
    private ModelNamespaceProviderInterface $modelNamespaceProvider;
    private ViewNamespacesContainerInterface $viewNamespacesContainer;

    public function __construct(
        // fixme move into config.
        string $namespaceNotResolvedErrorMessage = 'Model namespace cannot be resolved',
        ?ModelNamespaceProviderInterface $modelNamespaceProvider = null,
        ?ViewNamespacesContainerInterface $viewNamespacesContainer = null
    ) {
        $this->modelNamespaceProvider = null === $modelNamespaceProvider ?
            new ModelNamespaceProvider(new ObjectClassReader()) :
            $modelNamespaceProvider;
        $this->viewNamespacesContainer = null === $viewNamespacesContainer ?
            new ViewNamespacesContainer() :
            $viewNamespacesContainer;

        $this->namespaceNotFoundErrorMessage = $namespaceNotResolvedErrorMessage;
    }

    public function addNamespace(ViewNamespaceConfig $config): ViewNamespace
    {
        $viewNamespace = $this->makeViewNamespace($config);

        $this->viewNamespacesContainer->addViewNamespace($viewNamespace);

        return $viewNamespace;
    }

    public function makeModel(string $modelClass)
    {
        $modelNamespace = $this->modelNamespaceProvider->getModelNamespace($modelClass);
        $viewNamespace = $this->viewNamespacesContainer->getViewNamespaceByModelNamespace($modelNamespace);

        if (null === $viewNamespace) {
            throw $this->makeNamespaceNotResolvedException($modelNamespace);
        }

        $modelFactory = $viewNamespace->getConfig()->getModules()->getModelFactory();

        if (null === $modelFactory) {
            throw $this->makeNamespaceNotResolvedException($modelNamespace);
        }

        return $modelFactory->makeModel($modelClass);
    }

    public function renderModel($modelOrClass, Closure $setupCallback = null, bool $doPrint = false): string
    {
        $modelNamespace = $this->modelNamespaceProvider->getModelNamespace($modelOrClass);
        $viewNamespace = $this->viewNamespacesContainer->getViewNamespaceByModelNamespace($modelNamespace);

        if (null === $viewNamespace) {
            throw $this->makeNamespaceNotResolvedException($modelNamespace);
        }

        $modelRenderer = $viewNamespace->getConfig()->getModules()->getModelRenderer();

        if (null === $modelRenderer) {
            throw $this->makeNamespaceNotResolvedException($modelNamespace);
        }

        return $modelRenderer->renderModel($modelOrClass, $setupCallback, $doPrint);
    }

    protected function makeViewNamespace(ViewNamespaceConfig $config): ViewNamespace
    {
        return new ViewNamespace($config, $this, $this);
    }

    protected function makeNamespaceNotResolvedException(string $modelNamespace): Exception
    {
        $message = sprintf('%s : %s', $this->namespaceNotFoundErrorMessage, $modelNamespace);

        return new Exception($message);
    }
}
