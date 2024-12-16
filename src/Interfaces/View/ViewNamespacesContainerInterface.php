<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\View;

use Prosopo\Views\PrivateClasses\View\ViewNamespace;

interface ViewNamespacesContainerInterface
{
    public function addViewNamespace(ViewNamespace $viewNamespace): void;

    public function getViewNamespaceByModelNamespace(string $modelNamespace): ?ViewNamespace;
}
