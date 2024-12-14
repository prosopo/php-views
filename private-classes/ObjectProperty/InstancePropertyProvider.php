<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\ObjectProperty;

use Prosopo\Views\Interfaces\ObjectProperty\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\View\View;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
class InstancePropertyProvider implements PropertyValueProviderInterface
{
    private ViewFactoryInterface $viewFactory;

    public function __construct(ViewFactoryInterface $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    public function supports(string $type): bool
    {
        return null !== $this->getViewClassStringForInheritors($type);
    }

    public function getValue(string $type)
    {
        $viewClassString = $this->getViewClassStringForInheritors($type);

        if (null === $viewClassString) {
            return null;
        }

        return $this->viewFactory->makeView($viewClassString);
    }

    /**
     * @param class-string<View>|string $type
     *
     * @return class-string<View>|null
     */
    protected function getViewClassStringForInheritors(string $type)
    {
        return true === class_exists($type) &&
        true === is_a($type, View::class, true) ?
            $type :
            null;
    }
}
