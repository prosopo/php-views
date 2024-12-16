<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Object;

use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use ReflectionProperty;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class PropertyValueProvider implements PropertyValueProviderInterface
{
    public function supportsProperty(ReflectionProperty $property): bool
    {
        return false;
    }

    public function getPropertyValue(ReflectionProperty $property)
    {
        return null;
    }
}
