<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelWithDefaultsInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;

abstract class TemplateTemplateModel implements TemplateModelInterface, TemplateModelWithDefaultsInterface
{
    private ObjectReaderInterface $objectReader;
    private PropertyValueProviderInterface $propertyValueProviderForDefaults;

    /**
     * The constructor is marked as final to prevent accidental argument overrides.
     * This is essential for the ModelFactory, which automatically creates instances.
     *
     * To set custom default values for primitives, use the setCustomDefaults() method.
     *
     * If your Models require additional object dependencies, consider one of the following approaches:
     *
     * 1. Override the PropertyValueProvider module (recommended)
     *
     * This module is responsible for providing default values for model properties.
     * You can create your own implementation, for example,
     * to integrate with a Dependency Injection container like PHP-DI. This allows model properties to
     * be automatically resolved while object creation by your application's DI system.
     *
     * 2. Override the ModelFactory module (alternative)
     *
     * Alternatively, you can override the ModelFactory to integrate PHP-DI for resolving dependencies.
     * But in this approach, you need also to create a custom parent TemplateModel class
     * that implements TemplateModelInterface without the final constructor.
     */
    final public function __construct(
        ObjectReaderInterface $objectPropertyReader,
        PropertyValueProviderInterface $propertyValueProviderForDefaults
    ) {
        $this->objectReader = $objectPropertyReader;
        $this->propertyValueProviderForDefaults = $propertyValueProviderForDefaults;

        $this->setCustomDefaults();
    }

    public function getTemplateArguments(): array
    {
        return $this->objectReader->getObjectVariables($this);
    }

    public function getDefaultsPropertyValueProvider(): PropertyValueProviderInterface
    {
        return $this->propertyValueProviderForDefaults;
    }

    protected function setCustomDefaults(): void
    {
    }
}
