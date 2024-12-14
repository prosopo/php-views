<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\ObjectProperty\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
class ViewFactoryWithPropertyInitialization implements ViewFactoryInterface
{
    private ViewFactoryInterface $viewFactory;
    private ObjectPropertyManagerInterface $objectPropertyManager;
    private PropertyValueProviderInterface $propertyValueProvider;

    public function __construct(
        ViewFactoryInterface $viewFactory,
        ObjectPropertyManagerInterface $objectPropertyManager,
        PropertyValueProviderInterface $propertyValueProvider
    ) {
        $this->viewFactory = $viewFactory;
        $this->objectPropertyManager = $objectPropertyManager;
        $this->propertyValueProvider = $propertyValueProvider;
    }

    public function makeView(string $viewClass)
    {
        $view = $this->viewFactory->makeView($viewClass);

        $this->setDefaultValuesRecursively($view, $this->propertyValueProvider, $this->objectPropertyManager);

        return $view;
    }

    protected function setDefaultValuesRecursively(
        ViewInterface $view,
        PropertyValueProviderInterface $propertyValueProvider,
        ObjectPropertyManagerInterface $objectPropertyManager
    ): void {
        $this->setDefaultValues($view, $propertyValueProvider);

        $innerViews = $this->getInnerViews($view, $objectPropertyManager);

        array_map(function (ViewInterface $innerView) use ($propertyValueProvider, $objectPropertyManager) {
            $this->setDefaultValuesRecursively($innerView, $propertyValueProvider, $objectPropertyManager);
        }, $innerViews);
    }

    protected function setDefaultValues(
        ViewInterface $view,
        PropertyValueProviderInterface $propertyValueProvider
    ): void {
        $this->objectPropertyManager->setDefaultValues($view, $propertyValueProvider);
    }

    /**
     * @return ViewInterface[]
     */
    protected function getInnerViews(ViewInterface $view, ObjectPropertyManagerInterface $objectPropertyManager): array
    {
        $variables = $objectPropertyManager->getVariables($view);

        return array_filter($variables, function ($item) {
            return true === ($item instanceof ViewInterface);
        });
    }
}
