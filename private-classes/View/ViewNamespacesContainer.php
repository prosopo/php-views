<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Prosopo\Views\Interfaces\View\ViewNamespacesContainerInterface;

class ViewNamespacesContainer implements ViewNamespacesContainerInterface
{
    /**
     * @var array<string, ViewNamespace>
     */
    private array $viewNamespaces;

    public function __construct()
    {
        $this->viewNamespaces = [];
    }

    public function addViewNamespace(ViewNamespace $viewNamespace): void
    {
        $modelsRootNamespace = $viewNamespace->getConfig()->getModelsRootNamespace();
        $this->viewNamespaces[$modelsRootNamespace] = $viewNamespace;

        // Sort to ensure the rule is followed: more specific namespaces take precedence.
        // For example, /My/Package/Blade will be processed before the more generic /My/Package.
        uksort($this->viewNamespaces, function (string $key1, string $key2): int {
            return strlen($key2) <=> strlen($key1);
        });
    }

    public function getViewNamespaceByModelNamespace(string $modelNamespace): ?ViewNamespace
    {
        $matchedNamespaces = array_filter($this->viewNamespaces, function (
            ViewNamespace $viewNamespace,
            string $modelsRootNamespace
        ) use ($modelNamespace) {
            return 0 === strpos($modelNamespace, $modelsRootNamespace);
        }, ARRAY_FILTER_USE_BOTH);

        return count($matchedNamespaces) > 0 ?
            reset($matchedNamespaces) :
            null;
    }
}
