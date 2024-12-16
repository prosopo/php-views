<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Prosopo\Views\Interfaces\Views\ViewNamespaceInterface;
use Prosopo\Views\Interfaces\Views\ViewNamespacesContainerInterface;

class ViewNamespacesContainer implements ViewNamespacesContainerInterface
{
    /**
     * @var array<string, ViewNamespaceInterface>
     */
    private array $viewNamespaces;

    public function __construct()
    {
        $this->viewNamespaces = [];
    }

    public function addViewNamespace(ViewNamespaceInterface $viewNamespace): void
    {
        $modelsRootNamespace = $viewNamespace->getConfig()->getModelsRootNamespace();
        $this->viewNamespaces[$modelsRootNamespace] = $viewNamespace;

        // Sort to ensure the rule is followed: more specific namespaces take precedence.
        // For example, /My/Package/Blade will be processed before the more generic /My/Package.
        uksort($this->viewNamespaces, function (string $key1, string $key2): int {
            return strlen($key2) <=> strlen($key1);
        });
    }

    public function getViewNamespaceByModelNamespace(string $modelNamespace): ?ViewNamespaceInterface
    {
        $matchedNamespaces = array_filter($this->viewNamespaces, function (
            ViewNamespaceInterface $viewNamespace,
            string $modelsRootNamespace
        ) use ($modelNamespace) {
            return 0 === strpos($modelsRootNamespace, $modelNamespace);
        }, ARRAY_FILTER_USE_BOTH);

        return array_pop($matchedNamespaces);
    }
}
