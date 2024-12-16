<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Model;

use Prosopo\Views\Interfaces\Model\ModelNamespaceProviderInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelNamespaceProvider implements ModelNamespaceProviderInterface
{
    private ObjectClassReader $objectClassReader;

    public function __construct(ObjectClassReader $objectClassReader)
    {
        $this->objectClassReader = $objectClassReader;
    }

    public function getModelNamespace(TemplateModelInterface $model): string
    {
        $modelNamespaceWithClassName = $this->objectClassReader->getObjectClass($model);

        $lastDelimiterPosition = strrpos($modelNamespaceWithClassName, '\\');

        if (false === $lastDelimiterPosition) {
            return '';
        }

        $className = substr(
            $modelNamespaceWithClassName,
            $lastDelimiterPosition
        );

        return substr($modelNamespaceWithClassName, 0, -strlen($className));
    }
}
