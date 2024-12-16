<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Config\ViewNamespaceConfigInterface;
use Prosopo\Views\Interfaces\View\ViewNamespaceInterface;
use Prosopo\Views\PrivateClasses\View\ViewNamespacesContainer;

class ViewNamespacesContainerTest extends TestCase
{
    public function testGetViewNamespaceByModelNamespace(): void
    {
        // given
        $container = new ViewNamespacesContainer();

        $viewNamespaceMock1 = Mockery::mock(ViewNamespaceInterface::class);
        $configMock1 = Mockery::mock(ViewNamespaceConfigInterface::class);

        // when
        $getViewNamespace = fn() =>
        $container->getViewNamespaceByModelNamespace('App\\Package\\Specific\\Model');

        // then
        $configMock1
            ->shouldReceive('getModelsRootNamespace')
            ->andReturn('App\\Package');
        $viewNamespaceMock1
            ->shouldReceive('getConfig')
            ->andReturn($configMock1);

        // apply
        $container->addViewNamespace($viewNamespaceMock1);

        $this->assertEquals(
            'App\\Package',
            $getViewNamespace()->getConfig()->getModelsRootNamespace()
        );

        Mockery::close();
    }

    public function testGetViewNamespaceByModelNamespacePrefersLongerNamespace(): void
    {
        // given
        $container = new ViewNamespacesContainer();

        $viewNamespaceMock1 = Mockery::mock(ViewNamespaceInterface::class);
        $configMock1 = Mockery::mock(ViewNamespaceConfigInterface::class);

        $viewNamespaceMock2 = Mockery::mock(ViewNamespaceInterface::class);
        $configMock2 = Mockery::mock(ViewNamespaceConfigInterface::class);

        // when
        $getViewNamespace = fn() =>
        $container->getViewNamespaceByModelNamespace('App\\Package\\Specific\\Model');

        // then
        $configMock1
            ->shouldReceive('getModelsRootNamespace')
            ->andReturn('App\\Package');
        $viewNamespaceMock1
            ->shouldReceive('getConfig')
            ->andReturn($configMock1);

        $configMock2
            ->shouldReceive('getModelsRootNamespace')
            ->andReturn('App\\Package\\Specific');
        $viewNamespaceMock2
            ->shouldReceive('getConfig')
            ->andReturn($configMock2);

        // apply
        $container->addViewNamespace($viewNamespaceMock1);
        $container->addViewNamespace($viewNamespaceMock2);

        $this->assertEquals(
            'App\\Package\\Specific',
            $getViewNamespace()->getConfig()->getModelsRootNamespace()
        );

        Mockery::close();
    }

    public function testGetViewNamespaceByModelNamespaceReturnNullWhenNotFound(): void
    {
        // given
        $container = new ViewNamespacesContainer();

        $viewNamespaceMock1 = Mockery::mock(ViewNamespaceInterface::class);
        $configMock1 = Mockery::mock(ViewNamespaceConfigInterface::class);

        // when
        $getViewNamespace = fn() =>
        $container->getViewNamespaceByModelNamespace('Another\\Package');

        // then
        $configMock1
            ->shouldReceive('getModelsRootNamespace')
            ->andReturn('App\\Package');
        $viewNamespaceMock1
            ->shouldReceive('getConfig')
            ->andReturn($configMock1);

        // apply
        $container->addViewNamespace($viewNamespaceMock1);

        $this->assertNull($getViewNamespace());

        Mockery::close();
    }
}
