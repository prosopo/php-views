<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\ObjectProperty;

interface ObjectPropertyReaderInterface
{
    /**
     * @return array<string,mixed> name => value (or callback)
     */
    public function getVariables(object $instance): array;
}
