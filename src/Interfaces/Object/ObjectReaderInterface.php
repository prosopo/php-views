<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Object;

interface ObjectReaderInterface
{
    /**
     * @return array<string,mixed> name => value (or callback)
     */
    public function getObjectVariables(object $instance): array;
}
