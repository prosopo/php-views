<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Model;

use Closure;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class ModelFactoryWithSetupCallback implements ModelFactoryInterface
{
    private ModelFactoryInterface $modelFactory;

    public function __construct(ModelFactoryInterface $modelFactory)
    {
        $this->modelFactory = $modelFactory;
    }

    public function createModel(string $modelClass, ?Closure $setupModelCallback = null)
    {
        $model = $this->modelFactory->createModel($modelClass);

        if (null !== $setupModelCallback) {
            $setupModelCallback($model);
        }

        return $model;
    }
}
