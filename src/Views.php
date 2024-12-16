<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Closure;
use Exception;
use Prosopo\Views\Interfaces\Config\NamespaceConfigInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\Interfaces\Views\ViewsInterface;
use Prosopo\Views\Interfaces\Views\ViewsNamespaceInterface;
use Prosopo\Views\PrivateClasses\ViewsNamespace;

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
    private string $notFoundErrorMessage;

    public function __construct(string $notFoundErrorMessage = 'Namespace for the given View class is not registered')
    {
        $this->renderers = [];
        $this->factories = [];

        $this->notFoundErrorMessage = $notFoundErrorMessage;
    }

    public function addNamespace(NamespaceConfigInterface $config): ModulesInterface
    {
        $viewsNamespace = $this->makeViewsNamespace($config);

        $namespaceModules = $viewsNamespace->getModules();

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
        $factory = $this->getItemByClassName($modelClass, $this->factories);

        if (null === $factory) {
            throw $this->makeNamespaceNotRegisteredException($modelClass);
        }

        return $factory->makeModel($modelClass);
    }

    public function renderModel($modelOrClass, Closure $setupCallback = null, bool $doPrint = false): string
    {
        $viewClass = false === is_string($modelOrClass) ?
            get_class($modelOrClass) :
            $modelOrClass;

        $renderer = $this->getItemByClassName($viewClass, $this->renderers);

        if (null === $renderer) {
            throw $this->makeNamespaceNotRegisteredException($viewClass);
        }

        return $renderer->renderModel($modelOrClass, $setupCallback, $doPrint);
    }

    protected function makeViewsNamespace(NamespaceConfigInterface $config): ViewsNamespaceInterface
    {
        return new ViewsNamespace($config, $this, $this);
    }

    protected function makeNamespaceNotRegisteredException(string $viewClass): Exception
    {
        $message = sprintf('%s : %s', $this->notFoundErrorMessage, $viewClass);

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
     * @param class-string $className
     * @param array<string, T> $items
     *
     * @return T|null
     */
    protected function getItemByClassName(string $className, array $items)
    {
        $matchedItems = array_filter($items, function (
            $item,
            string $viewsRootNamespace
        ) use ($className) {
            return 0 === strpos($viewsRootNamespace, $className);
        }, ARRAY_FILTER_USE_BOTH);

        return array_pop($matchedItems);
    }
}
