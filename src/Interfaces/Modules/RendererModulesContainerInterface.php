<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Modules;

interface RendererModulesContainerInterface
{
    public function getModules(): RendererModulesInterface;
}
