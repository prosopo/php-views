<?php

declare(strict_types=1);

namespace Prosopo\Views;

use Prosopo\Views\Interfaces\View\ViewFactoryInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\Interfaces\ViewsInterface;
use Prosopo\Views\Template\TemplateProvider;
use Prosopo\Views\View\ViewFactory;
use Prosopo\Views\View\ViewRenderer;

class Views implements ViewsInterface
{
    private ViewRendererInterface $renderer;
    private ViewFactoryInterface $factory;

    public function __construct(ViewsConfig $config)
    {
        $objectPropertyManager = new ObjectPropertyManager();

        $templateProvider = new TemplateProvider(
            $config->getTemplatesRootPath(),
            $config->getTemplateFileExtension(),
            $config->getTemplateFileExtension()
        );
        $this->factory = new ViewFactory($objectPropertyManager, $templateProvider);

        $this->renderer = new ViewRenderer(
            $config->getTemplateRenderer(),
            $this->factory,
            $objectPropertyManager
        );
    }

    public function getFactory(): ViewFactoryInterface
    {
        return $this->factory;
    }

    public function getRenderer(): ViewRendererInterface
    {
        return $this->renderer;
    }
}
