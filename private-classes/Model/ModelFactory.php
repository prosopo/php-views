<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Model;

use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelFactory implements ModelFactoryInterface
{
    private ObjectReaderInterface $objectReader;
    private PropertyValueProviderInterface $propertyValueProvider;

    public function __construct(
        ObjectReaderInterface $objectReader,
        PropertyValueProviderInterface $propertyValueProvider
    ) {
        $this->objectReader = $objectReader;
        $this->propertyValueProvider = $propertyValueProvider;
    }

    public function createModel(string $modelClass)
    {
        return new $modelClass($this->objectReader, $this->propertyValueProvider);
    }
}
