<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Object;

use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use ReflectionProperty;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class PropertyValueProviderByTypes implements PropertyValueProviderInterface
{
    private PropertyValueProviderInterface $propertyValueProvider;
    /**
     * @var array<string,mixed> type => value
     */
    private array $valuesByType;

    /**
     * @param array<string,mixed> $defaultValues
     */
    public function __construct(PropertyValueProviderInterface $propertyValueProvider, array $defaultValues)
    {
        $this->propertyValueProvider = $propertyValueProvider;
        $this->valuesByType = $defaultValues;
    }

    public function supportsProperty(ReflectionProperty $property): bool
    {
        if ($this->propertyValueProvider->supportsProperty($property)) {
            return true;
        }

        $type = $this->getPropertyType($property);

        return key_exists($type, $this->valuesByType);
    }

    public function getPropertyValue(ReflectionProperty $property)
    {
        if ($this->propertyValueProvider->supportsProperty($property)) {
            return $this->propertyValueProvider->getPropertyValue($property);
        }

        $type = $this->getPropertyType($property);

        return key_exists($type, $this->valuesByType) ?
        $this->valuesByType[$type] :
        null;
    }

    protected function getPropertyType(ReflectionProperty $property): string
    {
        $reflectionType = $property->getType();

        return null !== $reflectionType ?
            // @phpstan-ignore-next-line
            $reflectionType->getName() :
            '';
    }
}
