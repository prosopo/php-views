<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Model;

use Closure;
use Exception;

interface ModelRendererInterface
{
    /**
     * @template T of TemplateModelInterface
     *
     * @param T|class-string<T> $modelOrClass
     * @param Closure(T):void|null $setupCallback
     *
     * @throws Exception
     */
    public function renderModel($modelOrClass, Closure $setupCallback = null, bool $doPrint = false): string;
}
