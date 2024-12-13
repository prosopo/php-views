<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\View;

use Closure;

interface ViewFactoryInterface
{
    /**
     * @template T of ViewInterface
     *
     * @param class-string<T> $viewClass
     * @param Closure(T):void|null $setupCallback
     */
    public function makeView(string $viewClass, ?Closure $setupCallback = null): ViewInterface;
}
