<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Views;

use Prosopo\Views\Interfaces\Modules\ViewNamespaceModulesInterface;
use Prosopo\Views\ViewNamespaceConfig;

interface ViewsInterface
{
    public function addNamespace(ViewNamespaceConfig $config): ViewNamespaceModulesInterface;
}
