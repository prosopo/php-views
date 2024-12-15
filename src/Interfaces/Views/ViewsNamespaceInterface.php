<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Views;

use Prosopo\Views\Interfaces\Modules\ModulesInterface;

interface ViewsNamespaceInterface
{
    public function getModules(): ModulesInterface;
}
