<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\View;

use Closure;

interface ViewRendererInterface
{
    /**
     * @template T of ViewInterface
     *
     * @param T|class-string<T> $viewOrClass
     * @param Closure(T):void|null $setupCallback
     */
    public function renderView($viewOrClass, Closure $setupCallback = null, bool $doPrint = false): string;
}
