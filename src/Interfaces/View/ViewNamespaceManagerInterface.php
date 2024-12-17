<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\View;

use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewNamespaceModules;

interface ViewNamespaceManagerInterface
{
    public function addNamespace(string $namespace, ViewNamespaceConfig $config): ViewNamespaceModules;
}
