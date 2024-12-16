<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Object;

use ReflectionProperty;

interface PropertyValueProviderInterface
{
    public function supportsProperty(ReflectionProperty $property): bool;

    /**
     * @return mixed
     */
    public function getPropertyValue(ReflectionProperty $property);
}
