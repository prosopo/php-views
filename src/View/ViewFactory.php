<?php

declare(strict_types=1);

namespace Prosopo\Views\View;

use Prosopo\Views\Interfaces\ObjectPropertyManagerInterface;
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;
use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;

class ViewFactory implements ViewFactoryInterface
{
    private ObjectPropertyManagerInterface $objectPropertyManager;
    private TemplateProviderInterface $templateProvider;

    public function __construct(
        ObjectPropertyManagerInterface $objectPropertyManager,
        TemplateProviderInterface $templateProvider
    ) {
        $this->objectPropertyManager = $objectPropertyManager;
        $this->templateProvider = $templateProvider;
    }

    public function makeView(string $viewClass): ViewInterface
    {
        $viewInstance = $this->makeViewInstance($viewClass);

        $this->objectPropertyManager->setDefaultValues($viewInstance);

        return $viewInstance;
    }

    /**
     * @template T of ViewInterface
     *
     * @param class-string<T> $viewClass
     *
     * @return T
     */
    protected function makeViewInstance(string $viewClass): ViewInterface
    {
        return new $viewClass($this->templateProvider);
    }
}
