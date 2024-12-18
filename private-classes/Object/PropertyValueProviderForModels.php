<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Object;

use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
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

        return null !== $this->getValidModelClass($type);
    }

    public function getPropertyValue(ReflectionProperty $property)
    {
        if (true === $this->propertyValueProvider->supportsProperty($property)) {
            return $this->propertyValueProvider->getPropertyValue($property);
        }

        $type = $this->getPropertyType($property);
        $modelClassString = $this->getValidModelClass($type);

        return null !== $modelClassString ?
             $this->modelFactory->createModel($modelClassString) :
            null;
    }

    /**
     * @param class-string<TemplateModelInterface>|string $propertyType
     *
     * @return class-string<TemplateModelInterface>|null
     */
    protected function getValidModelClass(string $propertyType)
    {
        return true === class_exists($propertyType) &&
        true === is_a($propertyType, TemplateModelInterface::class, true) ?
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
