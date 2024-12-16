<?php

namespace Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Config\ViewsNamespaceConfigInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;
use Prosopo\Views\Views;
use Prosopo\Views\ViewsNamespaceConfig;

class ViewsTest extends TestCase
{
    // fixme
    /*public function testRenderRespectsMoreSpecificNamespacePrecedenceRule(): void
    {
        // given
        $viewsMock = Mockery::mock(new Views())->makePartial();

        $objectClassReaderMock = Mockery::mock(ObjectClassReader::class);
        $shorterConfigMock = $this->getViewsNamespaceConfig();

        $longerFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $longerRendererMock = Mockery::mock(ModelRendererInterface::class);
        $longerConfigMock = $this->getViewsNamespaceConfig();
        $longerConfigMock->setModelsRootNamespace('My\Package\Blade');
        $longerConfigMock
            ->getModules()
            ->setModelFactory($longerFactoryMock)
            ->setModelRenderer($longerRendererMock);
        $model = Mockery::mock(TemplateModelInterface::class);

        // when
        $addShorterNamespace = fn()=>$viewsMock->addNamespace($shorterConfigMock);
        $addLongerNamespace = fn()=>$viewsMock->addNamespace($longerConfigMock);

        // then
        $objectClassReaderMock->shouldReceive('getObjectClass')
            ->andReturn('My\\Package\\Blade\\Model');
        $viewsMock->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getObjectClassReader')
            ->andReturn($objectClassReaderMock);
        $longerRendererMock->shouldReceive('renderModel')
            ->once()
            ->with($model)
            ->andReturn('RenderedLongerModel');

        // apply
        $addShorterNamespace();
        $addLongerNamespace();

        $this->assertEquals('RenderedLongerModel', $viewsMock->renderModel($model));

        Mockery::close();
    }*/

  /*  public function testMakeModelThrowsExceptionForUnknownFactory(): void
    {
        // given
        $views = new Views();

        // expect
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Namespace for the given View class is not registered');

        // when
        $views->makeModel('Unknown\\Model');

        Mockery::close();
    }

    public function testRenderModelThrowsExceptionForUnknownRenderer(): void
    {
        // given
        $views = new Views();

        // expect
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Namespace for the given View class is not registered');

        // when
        $views->renderModel('Unknown\\Model');

        Mockery::close();
    }

    public function testAddNamespaceReturnsModulesInterface(): void
    {
        // given
        $configMock = Mockery::mock(ViewsNamespaceConfigInterface::class);
        $configMock->shouldReceive('getModelsRootNamespace')
            ->andReturn('My/Package');

        $modulesMock = Mockery::mock(ModulesInterface::class);
        $modulesMock->shouldReceive('getModelFactory')->andReturn(Mockery::mock(ModelFactoryInterface::class));
        $modulesMock->shouldReceive('getModelRenderer')->andReturn(Mockery::mock(ModelRendererInterface::class));

        $viewsNamespaceMock = Mockery::mock('overload:' . ViewsNamespace::class);
        $viewsNamespaceMock->shouldReceive('getModules')->andReturn($modulesMock);

        $views = new Views();

        // when
        $result = $views->addNamespace($configMock);

        // then
        $this->assertInstanceOf(ModulesInterface::class, $result);
        $this->assertSame($modulesMock, $result);

        Mockery::close();
    }*/

    protected function getViewsNamespaceConfig(): ViewsNamespaceConfigInterface
    {
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);

        $viewsNamespaceConfigMock = new ViewsNamespaceConfig($templateRendererMock);

        return $viewsNamespaceConfigMock;
    }
}
