<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\View\ViewNamespaceModulesContainer;
use Prosopo\Views\View\ViewNamespaceModules;

class ViewNamespacesContainerTest extends TestCase
{
    public function testGetModulesByModelNamespace(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $viewNamespaceModules = new ViewNamespaceModules($templateRendererMock);
        $container = new ViewNamespaceModulesContainer();

        // when
        $container->addNamespaceModules('App\\Package\\Specific', $viewNamespaceModules);

        // then
        $resolvedModules = $container->getNamespaceModulesByModelNamespace('App\\Package\\Specific\\Model');

        $this->assertSame(
            $viewNamespaceModules,
            $resolvedModules
        );

        Mockery::close();
    }

    public function testGetModulesByModelNamespacePrefersLongerNamespace(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $shortNamespaceModules = new ViewNamespaceModules($templateRendererMock);
        $longNamespaceModules = new ViewNamespaceModules($templateRendererMock);
        $container = new ViewNamespaceModulesContainer();

        // when
        $container->addNamespaceModules('App\\Package', $shortNamespaceModules);
        $container->addNamespaceModules('App\\Package\\Specific', $longNamespaceModules);

        // then
        $resolvedModules = $container->getNamespaceModulesByModelNamespace('App\\Package\\Specific\\Model');

        $this->assertSame(
            $longNamespaceModules,
            $resolvedModules
        );

        Mockery::close();
    }

    public function testGetModulesByModelNamespaceReturnsNullWhenNotFound(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $viewNamespaceModules = new ViewNamespaceModules($templateRendererMock);
        $container = new ViewNamespaceModulesContainer();

        // when
        $container->addNamespaceModules('App\\Package\\Specific', $viewNamespaceModules);

        // then
        $resolvedModules = $container->getNamespaceModulesByModelNamespace('Another\\Package');

        $this->assertNull(
            $resolvedModules
        );

        Mockery::close();
    }
}
