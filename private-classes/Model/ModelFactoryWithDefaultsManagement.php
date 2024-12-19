<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Model;

use Closure;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelWithDefaultsInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelFactoryWithDefaultsManagement implements ModelFactoryInterface
{
    private ModelFactoryInterface $modelFactory;
    private ObjectReaderInterface $objectPropertyReader;
    private ObjectPropertyWriterInterface $objectPropertyWriter;

    public function __construct(
        ModelFactoryInterface $modelFactory,
        ObjectReaderInterface $objectReader,
        ObjectPropertyWriterInterface $objectPropertyWriter
    ) {
        $this->modelFactory = $modelFactory;
        $this->objectPropertyReader = $objectReader;
        $this->objectPropertyWriter = $objectPropertyWriter;
    }

    public function createModel(string $modelClass, ?Closure $setupModelCallback = null)
    {
        $model = $this->modelFactory->createModel($modelClass);

        if (true === ($model instanceof TemplateModelWithDefaultsInterface)) {
            $this->setDefaultValuesRecursively($model);
        }

        return $model;
    }

    protected function setDefaultValuesRecursively(TemplateModelWithDefaultsInterface $modelWithDefaults): void
    {
        $defaultsPropertyValueProvider = $modelWithDefaults->getDefaultPropertyValueProvider();

        $this->objectPropertyWriter->assignPropertyValues($modelWithDefaults, $defaultsPropertyValueProvider);

        $innerModelsWithDefaults = $this->getInnerModels(
            $this->objectPropertyReader->extractObjectVariables($modelWithDefaults)
        );

        array_map(function (TemplateModelWithDefaultsInterface $innerModelWithDefaults) {
            $this->setDefaultValuesRecursively($innerModelWithDefaults);
        }, $innerModelsWithDefaults);
    }

    /**
     * @param array<string,mixed> $variables
     *
     * @return TemplateModelWithDefaultsInterface[]
     */
    protected function getInnerModels(array $variables): array
    {
        return array_filter($variables, function ($item) {
            return true === ($item instanceof TemplateModelWithDefaultsInterface) ;
        });
    }
}
