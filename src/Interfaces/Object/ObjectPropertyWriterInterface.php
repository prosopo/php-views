<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Object;

interface ObjectPropertyWriterInterface
{
    public function setObjectPropertyValues(
        object $instance,
        PropertyValueProviderInterface $propertyValueProvider
    ): void;
}
