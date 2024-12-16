<?php

namespace Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Config\ViewNamespaceConfigInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\ViewNamespaceConfig;

class ViewsTest extends TestCase
{
    // fixme
    protected function getViewsNamespaceConfig(): ViewNamespaceConfigInterface
    {
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);

        $viewsNamespaceConfigMock = new ViewNamespaceConfig($templateRendererMock);

        return $viewsNamespaceConfigMock;
    }
}
