<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Object;

use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\TemplateTemplateModel;
use ReflectionProperty;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class PropertyValueProviderForModels implements PropertyValueProviderInterface
{
    private PropertyValueProviderInterface $propertyValueProvider;
    private ModelFactoryInterface $modelFactory;

    public function __construct(
        PropertyValueProviderInterface $propertyValueProvider,
        ModelFactoryInterface $viewFactory
    ) {
        $this->propertyValueProvider = $propertyValueProvider;
        $this->modelFactory = $viewFactory;
    }

    public function supportsProperty(ReflectionProperty $property): bool
    {
        if (true === $this->propertyValueProvider->supportsProperty($property)) {
            return true;
        }

        $type = $this->getPropertyType($property);

        return null !== $this->getModelClassStringForInheritors($type);
    }

    public function getPropertyValue(ReflectionProperty $property)
    {
        if (true === $this->propertyValueProvider->supportsProperty($property)) {
            return $this->propertyValueProvider->getPropertyValue($property);
        }

        $type = $this->getPropertyType($property);
        $modelClassString = $this->getModelClassStringForInheritors($type);

        return null !== $modelClassString ?
             $this->modelFactory->makeModel($modelClassString) :
            null;
    }

    /**
     * @param class-string<TemplateTemplateModel>|string $propertyType
     *
     * @return class-string<TemplateTemplateModel>|null
     */
    protected function getModelClassStringForInheritors(string $propertyType)
    {
        return true === class_exists($propertyType) &&
        true === is_a($propertyType, TemplateTemplateModel::class, true) ?
            $propertyType :
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
