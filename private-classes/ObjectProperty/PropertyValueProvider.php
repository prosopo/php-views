<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\ObjectProperty;

use Prosopo\Views\Interfaces\ObjectProperty\PropertyValueProviderInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class PropertyValueProvider implements PropertyValueProviderInterface
{
    private PropertyValueProviderInterface $instancePropertyProvider;
    /**
     * @var array<string,mixed> type => value
     */
    private array $valuesByType;

    /**
     * @param array<string,mixed> $defaultValues
     */
    public function __construct(PropertyValueProviderInterface $instancePropertyProvider, array $defaultValues)
    {
        $this->instancePropertyProvider = $instancePropertyProvider;
        $this->valuesByType = $defaultValues;
    }

    public function supports(string $type): bool
    {
        return true === key_exists($type, $this->valuesByType) ||
            true === $this->instancePropertyProvider->supports($type) ;
    }

    public function getValue(string $type)
    {
        if (true === key_exists($type, $this->valuesByType)) {
            return $this->valuesByType[$type];
        }

        return $this->instancePropertyProvider->getValue($type);
    }
}
