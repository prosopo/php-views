<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\View;

use Prosopo\Views\View\ViewNamespaceModules;

interface ViewNamespaceModulesContainerInterface
{
    public function addNamespaceModules(string $namespace, ViewNamespaceModules $viewNamespaceModules): void;

    public function getNamespaceModulesByModelNamespace(string $modelNamespace): ?ViewNamespaceModules;
}
