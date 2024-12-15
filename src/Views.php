<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Closure;
use Exception;
use Prosopo\Views\Interfaces\Config\NamespaceConfigInterface;
use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\Interfaces\Views\ViewsInterface;
use Prosopo\Views\Interfaces\Views\ViewsNamespaceInterface;
use Prosopo\Views\PrivateClasses\ViewsNamespace;

/**
 * This class is marked as a final to prevent anyone from extending it.
 * We reserve the right to change its private and protected methods, properties and introduce new public ones.
 */
final class Views implements ViewsInterface, ViewFactoryInterface, ViewRendererInterface
{
    /**
     * @var array<string, ViewFactoryInterface> namespace => ViewRendererInterface
     */
    private array $factories;
    /**
     * @var array<string, ViewRendererInterface> namespace => ViewFactoryInterface
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

        $namespaceFactory = $namespaceModules->getViewFactory();
        $namespaceRenderer = $namespaceModules->getViewRenderer();

        if (
            null === $namespaceFactory ||
            null === $namespaceRenderer
        ) {
            return $namespaceModules;
        }

        // Save the original Factory and Renderer.

        $this->factories[$config->getViewsRootNamespace()] = $namespaceFactory;
        $this->renderers[$config->getViewsRootNamespace()] = $namespaceRenderer;

        // Sort to ensure the rule is followed: more specific namespaces take precedence.
        // For example, /My/Package/Blade will be processed before the more generic /My/Package.

        $this->factories = $this->sortArrayByKeyLengthDesc($this->factories);
        $this->renderers = $this->sortArrayByKeyLengthDesc($this->renderers);

        return $namespaceModules;
    }

    public function makeView(string $viewClass)
    {
        $factory = $this->getItemByClassName($viewClass, $this->factories);

        if (null === $factory) {
            throw $this->makeNamespaceNotRegisteredException($viewClass);
        }

        return $factory->makeView($viewClass);
    }

    public function renderView($viewOrClass, Closure $setupCallback = null, bool $doPrint = false): string
    {
        $viewClass = false === is_string($viewOrClass) ?
            get_class($viewOrClass) :
            $viewOrClass;

        $renderer = $this->getItemByClassName($viewClass, $this->renderers);

        if (null === $renderer) {
            throw $this->makeNamespaceNotRegisteredException($viewClass);
        }

        return $renderer->renderView($viewOrClass, $setupCallback, $doPrint);
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
