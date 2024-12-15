<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Views;

use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\NamespaceConfig;

interface ViewsInterface
{
    public function addNamespace(NamespaceConfig $config): ModulesInterface;
}
