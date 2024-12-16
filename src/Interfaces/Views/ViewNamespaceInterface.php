<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Views;

use Prosopo\Views\Interfaces\Config\ViewNamespaceConfigInterface;

interface ViewNamespaceInterface
{
    public function getConfig(): ViewNamespaceConfigInterface;
}
