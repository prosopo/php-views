<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Views;

use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\ViewsNamespaceConfig;

interface ViewsInterface
{
    public function addNamespace(ViewsNamespaceConfig $config): ModulesInterface;
}
