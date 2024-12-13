<?php

declare(strict_types=1);

namespace Prosopo\Views\View;

use Closure;
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

    public function makeView(string $viewClass, ?Closure $setupCallback = null): ViewInterface
    {
        $viewInstance = new $viewClass($this->templateProvider);

        $this->objectPropertyManager->setDefaultValues($viewInstance);

        if (null !== $setupCallback) {
            $setupCallback($viewInstance);
        }

        return $viewInstance;
    }
}
