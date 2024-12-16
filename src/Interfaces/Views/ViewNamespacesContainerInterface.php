<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Views;

interface ViewNamespacesContainerInterface
{
    public function addViewNamespace(ViewNamespaceInterface $viewNamespace): void;

    public function getViewNamespaceByModelNamespace(string $modelNamespace): ?ViewNamespaceInterface;
}
