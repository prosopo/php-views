<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\View;

use Prosopo\Views\View\ViewNamespaceModules;

interface ViewNamespaceModulesContainerInterface
{
    public function registerNamespaceModules(string $namespace, ViewNamespaceModules $viewNamespaceModules): void;

    public function resolveNamespaceModules(string $modelNamespace): ?ViewNamespaceModules;
}
