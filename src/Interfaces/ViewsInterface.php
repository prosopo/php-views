<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces;

use Prosopo\Views\Modules;
use Prosopo\Views\NamespaceConfig;

interface ViewsInterface
{
    public function addNamespace(NamespaceConfig $config): Modules;
}
