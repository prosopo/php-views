<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Model;

use Closure;

interface ModelRendererInterface
{
    /**
     * @template T of TemplateModelInterface
     *
     * @param T|class-string<T> $modelOrClass
     * @param Closure(T):void|null $setupCallback
     */
    public function renderModel($modelOrClass, Closure $setupCallback = null, bool $doPrint = false): string;
}
