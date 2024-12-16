<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Closure;
use Exception;
use Prosopo\Views\Interfaces\Config\ViewNamespaceConfigInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Modules\ViewNamespaceModulesInterface;
use Prosopo\Views\Interfaces\Views\ViewNamespaceInterface;
use Prosopo\Views\Interfaces\Views\ViewsInterface;
use Prosopo\Views\PrivateClasses\Model\ModelNamespaceProvider;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;
use Prosopo\Views\PrivateClasses\View\ViewNamespace;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class Views implements ViewsInterface, ModelFactoryInterface, ModelRendererInterface
{
    /**
     * @var array<string, ModelFactoryInterface> namespace => ViewRendererInterface
     */
    private array $factories;
    /**
     * @var array<string, ModelRendererInterface> namespace => ViewFactoryInterface
     */
    private array $renderers;
    private string $namespaceNotFoundErrorMessage;
    private ModelNamespaceProviderInterface $modelNamespaceProvider;

    public function __construct(
        // fixme move into config.
        string $namespaceNotFoundErrorMessage = 'Namespace for the given Model is not registered',
        ?ModelNamespaceProviderInterface $modelNamespaceProvider = null
    ) {
        $this->renderers = [];
        $this->factories = [];
        $this->modelNamespaceProvider = null === $modelNamespaceProvider ?
            new ModelNamespaceProvider(new ObjectClassReader()) :
            $modelNamespaceProvider;

        $this->namespaceNotFoundErrorMessage = $namespaceNotFoundErrorMessage;
    }

    public function addNamespace(ViewNamespaceConfigInterface $config): ViewNamespaceModulesInterface
    {
        $viewNamespace = $this->makeViewNamespace($config);

        $namespaceModules = $viewNamespace->getModules();

        $namespaceFactory = $namespaceModules->getModelFactory();
        $namespaceRenderer = $namespaceModules->getModelRenderer();

        if (
            null === $namespaceFactory ||
            null === $namespaceRenderer
        ) {
            return $namespaceModules;
        }

        // Save the original Factory and Renderer.

        $this->factories[$config->getModelsRootNamespace()] = $namespaceFactory;
        $this->renderers[$config->getModelsRootNamespace()] = $namespaceRenderer;

        // Sort to ensure the rule is followed: more specific namespaces take precedence.
        // For example, /My/Package/Blade will be processed before the more generic /My/Package.

        $this->factories = $this->sortArrayByKeyLengthDesc($this->factories);
        $this->renderers = $this->sortArrayByKeyLengthDesc($this->renderers);

        return $namespaceModules;
    }

    public function makeModel(string $modelClass)
    {
        $modelNamespace = $this->modelNamespaceProvider->getModelNamespace($modelClass);

        $factory = $this->getItemByKeyMatch($modelNamespace, $this->factories);

        if (null === $factory) {
            throw $this->makeNamespaceNotRegisteredException($modelNamespace);
        }

        return $factory->makeModel($modelClass);
    }

    public function renderModel($modelOrClass, Closure $setupCallback = null, bool $doPrint = false): string
    {
        $modelNamespace = $this->modelNamespaceProvider->getModelNamespace($modelOrClass);

        $renderer = $this->getItemByKeyMatch($modelNamespace, $this->renderers);

        if (null === $renderer) {
            throw $this->makeNamespaceNotRegisteredException($modelNamespace);
        }

        return $renderer->renderModel($modelOrClass, $setupCallback, $doPrint);
    }

    protected function makeViewNamespace(ViewNamespaceConfigInterface $config): ViewNamespaceInterface
    {
        return new ViewNamespace($config, $this, $this);
    }

    // fixme move the methods below into separate class.

    protected function makeNamespaceNotRegisteredException(string $namespace): Exception
    {
        $message = sprintf('%s : %s', $this->namespaceNotFoundErrorMessage, $namespace);

        return new Exception($message);
    }

    /**
     * @template T
     *
     * @param array<string, T> $array
     *
     * @return array<string, T>
     */
    protected function sortArrayByKeyLengthDesc(array $array): array
    {
        uksort($array, function (string $key1, string $key2): int {
            return strlen($key2) <=> strlen($key1);
        });

        return $array;
    }

    /**
     * @template T
     *
     * @param string $key
     * @param array<string, T> $items
     *
     * @return T|null
     */
    protected function getItemByKeyMatch(string $key, array $items)
    {
        $matchedItems = array_filter($items, function (
            $item,
            string $itemKey
        ) use ($key) {
            return 0 === strpos($itemKey, $key);
        }, ARRAY_FILTER_USE_BOTH);

        return array_pop($matchedItems);
    }
}
