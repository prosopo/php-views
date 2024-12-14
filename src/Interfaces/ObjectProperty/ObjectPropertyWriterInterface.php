<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\ObjectProperty;

interface ObjectPropertyWriterInterface
{
    public function setDefaultValues(
        object $instance,
        ?PropertyValueProviderInterface $propertyValueProvider = null
    ): void;
}
