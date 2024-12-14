<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\View;

interface ViewFactoryInterface
{
    /**
     * @template T of ViewInterface
     *
     * @param class-string<T> $viewClass
     *
     * @return T
     */
    public function makeView(string $viewClass);
}
