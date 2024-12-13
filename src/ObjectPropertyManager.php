<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\ObjectPropertyManagerInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class ObjectPropertyManager implements ObjectPropertyManagerInterface
{
    public const DEFAULT_VALUES = array(
        'array'  => array(),
        'bool'   => false,
        'float'  => 0.0,
        'int'    => 0,
        'object' => null,
        'string' => '',
    );

    /**
     * @var array<string,mixed> $defaultValues type => default_value
     */
    private array $defaultValues;

    /**
     * @param array<string,mixed> $defaultValues type => default_value
     */
    public function __construct(array $defaultValues = self::DEFAULT_VALUES)
    {
        $this->defaultValues = $defaultValues;
    }

    public function setDefaultValues(object $instance): void
    {
        $reflectionClass       = $this->getReflectionClass($instance);
        $publicTypedVariables = $this->getPublicTypedVariables($reflectionClass);

        array_map(
            function (ReflectionProperty $reflection_property) use ($instance) {
                if (true === $reflection_property->isInitialized($instance)) {
                    return;
                }

                $this->setDefaultValueWhenTypeIsSupported($instance, $reflection_property);
            },
            $publicTypedVariables
        );
    }

    public function getVariables(object $instance): array
    {
        $reflectionClass = $this->getReflectionClass($instance);

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
     * @return ReflectionClass<object>
     */
    protected function getReflectionClass(object $instance): ReflectionClass
    {
        return new ReflectionClass($instance);
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

    protected function setDefaultValueWhenTypeIsSupported(
        object $instance,
        ReflectionProperty $reflectionProperty
    ): void {
        $type = $reflectionProperty->getType();

        $typeName = null !== $type ?
            // @phpstan-ignore-next-line
            $type->getName() :
            '';

        if (false === key_exists($typeName, $this->defaultValues)) {
            return;
        }

        $reflectionProperty->setValue($instance, $this->defaultValues[ $typeName ]);
    }
}
