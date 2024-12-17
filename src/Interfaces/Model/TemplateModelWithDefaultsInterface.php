<?php

declare(strict_types=1);

namespace Prosopo\Views\Interfaces\Model;

use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;

interface TemplateModelWithDefaultsInterface
{
    public function getDefaultsPropertyValueProvider(): PropertyValueProviderInterface;
}
