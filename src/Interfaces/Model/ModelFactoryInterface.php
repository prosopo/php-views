<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Model;

use Closure;
use Exception;

interface ModelFactoryInterface
{
    /**
     * @template T of TemplateModelInterface
     *
     * @param class-string<T> $modelClass
     * @param Closure(T):void|null $setupModelCallback
     *
     * @return T
     *
     * @throws Exception
     */
    public function createModel(string $modelClass, ?Closure $setupModelCallback = null);
}
