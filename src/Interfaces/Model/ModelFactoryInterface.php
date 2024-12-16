<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Model;

interface ModelFactoryInterface
{
    /**
     * @template T of TemplateModelInterface
     *
     * @param class-string<T> $modelClass
     *
     * @return T
     */
    public function makeModel(string $modelClass);
}
