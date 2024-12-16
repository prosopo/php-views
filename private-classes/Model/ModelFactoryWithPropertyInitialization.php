<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Model;

use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelFactoryWithPropertyInitialization implements ModelFactoryInterface
{
    private ModelFactoryInterface $modelFactory;
    private ObjectReaderInterface $objectPropertyReader;
    private ObjectPropertyWriterInterface $objectPropertyWriter;
    private PropertyValueProviderInterface $propertyValueProvider;

    public function __construct(
        ModelFactoryInterface $modelFactory,
        ObjectReaderInterface $objectReader,
        ObjectPropertyWriterInterface $objectPropertyWriter,
        PropertyValueProviderInterface $propertyValueProvider
    ) {
        $this->modelFactory = $modelFactory;
        $this->objectPropertyReader = $objectReader;
        $this->objectPropertyWriter = $objectPropertyWriter;
        $this->propertyValueProvider = $propertyValueProvider;
    }

    public function makeModel(string $modelClass)
    {
        $model = $this->modelFactory->makeModel($modelClass);

        $this->setDefaultValuesRecursively($model);

        return $model;
    }

    protected function setDefaultValuesRecursively(TemplateModelInterface $model): void
    {
        $this->objectPropertyWriter->setObjectPropertyValues($model, $this->propertyValueProvider);

        $innerModels = $this->getInnerModels($this->objectPropertyReader->getObjectVariables($model));

        array_map(function (TemplateModelInterface $innerModel) {
            $this->setDefaultValuesRecursively($innerModel);
        }, $innerModels);
    }

    /**
     * @param array<string,mixed> $variables
     *
     * @return TemplateModelInterface[]
     */
    protected function getInnerModels(array $variables): array
    {
        return array_filter($variables, function ($item) {
            return true === ($item instanceof TemplateModelInterface) ;
        });
    }
}
