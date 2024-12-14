<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\ObjectProperty;

interface PropertyValueProviderInterface
{
    public function supports(string $type): bool;

    /**
     * @return mixed
     */
    public function getValue(string $type);
}
