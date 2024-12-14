<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces;

use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\Modules;

interface ViewsInterface
{
    public function getFactory(): ViewFactoryInterface;

    public function getRenderer(): ViewRendererInterface;

    public function getModules(): Modules;
}
