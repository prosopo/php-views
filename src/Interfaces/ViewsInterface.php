<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces;

use Prosopo\Views\PrivateClasses\View\ViewNamespace;
use Prosopo\Views\ViewNamespaceConfig;

interface ViewsInterface
{
    public function addNamespace(ViewNamespaceConfig $config): ViewNamespace;
}
