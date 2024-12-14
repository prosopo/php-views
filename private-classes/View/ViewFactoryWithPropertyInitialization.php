<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\View;

use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyReaderInterface;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyWriterInterface;
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
    private ObjectPropertyReaderInterface $objectPropertyReader;
    private ObjectPropertyWriterInterface $objectPropertyWriter;
    private PropertyValueProviderInterface $propertyValueProvider;

    public function __construct(
        ViewFactoryInterface $viewFactory,
        ObjectPropertyReaderInterface $objectPropertyReader,
        ObjectPropertyWriterInterface $objectPropertyWriter,
        PropertyValueProviderInterface $propertyValueProvider
    ) {
        $this->viewFactory = $viewFactory;
        $this->objectPropertyReader = $objectPropertyReader;
        $this->objectPropertyWriter = $objectPropertyWriter;
        $this->propertyValueProvider = $propertyValueProvider;
    }

    public function makeView(string $viewClass)
    {
        $view = $this->viewFactory->makeView($viewClass);

        $this->setDefaultValuesRecursively($view);

        return $view;
    }

    protected function setDefaultValuesRecursively(ViewInterface $view): void
    {
        $this->objectPropertyWriter->setDefaultValues($view, $this->propertyValueProvider);

        $innerViews = $this->getInnerViews($this->objectPropertyReader->getVariables($view));

        array_map(function (ViewInterface $innerView) {
            $this->setDefaultValuesRecursively($innerView);
        }, $innerViews);
    }

    /**
     * @param array<string,mixed> $variables
     *
     * @return ViewInterface[]
     */
    protected function getInnerViews(array $variables): array
    {
        return array_filter($variables, function ($item) {
            return true === ($item instanceof ViewInterface);
        });
    }
}
