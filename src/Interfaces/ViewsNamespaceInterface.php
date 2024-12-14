<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces;

use Prosopo\Views\Modules;

interface ViewsNamespaceInterface
{
    public function getModules(): Modules;
}
