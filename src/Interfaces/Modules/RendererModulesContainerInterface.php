<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Modules;

use Prosopo\Views\Interfaces\Modules\RendererModulesInterface;

interface RendererModulesContainerInterface
{
    public function getModules(): RendererModulesInterface;
}
