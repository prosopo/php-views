<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Object;

use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ObjectPropertyWriter implements ObjectPropertyWriterInterface
{
    public function setObjectPropertyValues(
        object $instance,
        ?PropertyValueProviderInterface $propertyValueProvider = null
    ): void {
        $reflectionClass       = new ReflectionClass($instance);
        $publicTypedVariables = $this->getPublicTypedVariables($reflectionClass);

        array_map(
            function (ReflectionProperty $reflectionProperty) use ($instance, $propertyValueProvider) {
                if (true === $reflectionProperty->isInitialized($instance)) {
                    return;
                }

                $isDefaultValueSet = null !== $propertyValueProvider &&
                    $this->setDefaultValueForSupportedType(
                        $instance,
                        $propertyValueProvider,
                        $reflectionProperty
                    );

                if (false === $isDefaultValueSet) {
                    $this->setNullForNullableProperty($instance, $reflectionProperty);
                }
            },
            $publicTypedVariables
        );
    }

    /**
     * @param ReflectionClass<object> $reflection_class
     *
     * @return ReflectionProperty[]
     */
    protected function getPublicTypedVariables(ReflectionClass $reflection_class): array
    {
        $publicProperties = $reflection_class->getProperties(ReflectionProperty::IS_PUBLIC);

        return $this->getTypedProperties($publicProperties);
    }

    protected function setDefaultValueForSupportedType(
        object $instance,
        PropertyValueProviderInterface $propertyValueProvider,
        ReflectionProperty $reflectionProperty
    ): bool {
        if (false === $propertyValueProvider->supportsProperty($reflectionProperty)) {
            return false;
        }

        $value = $propertyValueProvider->getPropertyValue($reflectionProperty);

        $reflectionProperty->setValue($instance, $value);

        return true;
    }

    protected function setNullForNullableProperty(object $instance, ReflectionProperty $reflectionProperty): void
    {
        $type = $reflectionProperty->getType();

        if (
            null === $type ||
            false === $type->allowsNull()
        ) {
            return;
        }

        $reflectionProperty->setValue($instance, null);
    }

    /**
     * @param ReflectionProperty[] $reflectionProperties
     *
     * @return ReflectionProperty[]
     */
    protected function getTypedProperties(array $reflectionProperties): array
    {
        return array_filter(
            $reflectionProperties,
            function (ReflectionProperty $property): bool {
                return null !== $property->getType();
            }
        );
    }
}
