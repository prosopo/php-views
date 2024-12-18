<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Object;

use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ObjectReader implements ObjectReaderInterface
{
    public function extractObjectVariables(object $instance): array
    {
        $reflectionClass = new ReflectionClass($instance);

        $publicTypedVariables = $this->getPublicTypedVariables($reflectionClass);
        $variableValues        = $this->getPropertyValues($instance, $publicTypedVariables);

        $methodNames     = $this->getPublicMethodNames($reflectionClass);
        $methodCallbacks = $this->makeMethodCallbacks($instance, $methodNames);

        /**
         * @var array<string,mixed>
         */
        return array_merge($variableValues, $methodCallbacks);
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

    /**
     * @param ReflectionClass<object> $reflection_class
     *
     * @return string[]
     */
    protected function getPublicMethodNames(ReflectionClass $reflection_class): array
    {
        $publicMethods = $reflection_class->getMethods(ReflectionMethod::IS_PUBLIC);

        return array_diff(
            $this->extractMethodNames($publicMethods),
            array( '__construct' )
        );
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

    /**
     * @param ReflectionMethod[] $reflectionMethods
     *
     * @return string[]
     */
    protected function extractMethodNames(array $reflectionMethods): array
    {
        return array_map(
            function (ReflectionMethod $method) {
                return $method->getName();
            },
            $reflectionMethods
        );
    }

    /**
     * @param ReflectionProperty[] $reflectionProperties
     *
     * @return array<string,mixed> variableName => variableValue
     */
    protected function getPropertyValues(object $instance, array $reflectionProperties): array
    {
        return array_reduce(
            $reflectionProperties,
            function (array $variableValues, ReflectionProperty $reflection_property) use ($instance) {
                $variableValues[ $reflection_property->getName() ] = $reflection_property->getValue($instance);

                return $variableValues;
            },
            array()
        );
    }

    /**
     * @param string[] $methodNames
     *
     * @return array<string,callable> methodName => method
     */
    protected function makeMethodCallbacks(object $instance, array $methodNames): array
    {
        return array_reduce(
            $methodNames,
            function (array $methodCallbacks, string $method_name) use ($instance) {
                $methodCallbacks[ $method_name ] = array( $instance, $method_name );

                return $methodCallbacks;
            },
            array()
        );
    }
}
