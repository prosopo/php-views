<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\ObjectProperty;

interface ObjectPropertyManagerInterface
{
    public function setDefaultValues(
        object $instance,
        ?PropertyValueProviderInterface $propertyValueProvider = null
    ): void;

    /**
     * @return array<string,mixed> name => value (or callback)
     */
    public function getVariables(object $instance): array;
}
