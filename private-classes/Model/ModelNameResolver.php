<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Model;

use Prosopo\Views\Interfaces\Model\ModelNameResolverInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelNameResolver implements ModelNameResolverInterface
{
    private ObjectClassReader $objectClassReader;

    public function __construct(ObjectClassReader $objectClassReader)
    {
        $this->objectClassReader = $objectClassReader;
    }

    public function resolveModelName(TemplateModelInterface $model): string
    {
        $modelNamespaceWithClassName = $this->objectClassReader->getObjectClass($model) ;

        $lastDelimiterPosition = strrpos($modelNamespaceWithClassName, '\\');

        if (false === $lastDelimiterPosition) {
            return '';
        }

        return substr($modelNamespaceWithClassName, $lastDelimiterPosition + 1);
    }
}