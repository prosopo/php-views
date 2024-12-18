<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateResolverInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Model\ModelRenderer;

class ModelRendererTest extends TestCase
{
    public function testRenderModelWithModelInstance(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $templateProviderMock = Mockery::mock(ModelTemplateResolverInterface::class);
        $modelMock = Mockery::mock(TemplateModelInterface::class);
        $renderer = new ModelRenderer(
            $templateRendererMock,
            $modelFactoryMock,
            $templateProviderMock
        );

        // when
        $result = fn() => $renderer->renderModel($modelMock);

        // then
        $modelMock->shouldReceive('getTemplateArguments')
            ->once()
            ->andReturn(['key' => 'value']);

        $templateProviderMock->shouldReceive('resolveModelTemplate')
            ->once()
            ->with($modelMock)
            ->andReturn('<div>{{ $key }}</div>');

        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<div>{{ $key }}</div>', ['key' => 'value'], false)
            ->andReturn('<div>value</div>');

        // then
        $this->assertSame('<div>value</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderModelWithModelClass(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $templateProviderMock = Mockery::mock(ModelTemplateResolverInterface::class);
        $modelMock = Mockery::mock(TemplateModelInterface::class);
        $renderer = new ModelRenderer(
            $templateRendererMock,
            $modelFactoryMock,
            $templateProviderMock
        );

        // when
        $result = fn() => $renderer->renderModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('createModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($modelMock);

        $modelMock->shouldReceive('getTemplateArguments')
            ->once()
            ->andReturn(['key' => 'value']);

        $templateProviderMock->shouldReceive('resolveModelTemplate')
            ->once()
            ->with($modelMock)
            ->andReturn('<div>{{ $key }}</div>');

        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<div>{{ $key }}</div>', ['key' => 'value'], false)
            ->andReturn('<div>value</div>');

        $this->assertSame('<div>value</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderModelAppliesSetupCallback(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $templateProviderMock = Mockery::mock(ModelTemplateResolverInterface::class);
        $modelMock = Mockery::mock(TemplateModelInterface::class);
        $renderer = new ModelRenderer(
            $templateRendererMock,
            $modelFactoryMock,
            $templateProviderMock
        );
        $setupCallback = function ($model) {
            $model->key = 'modified_value';
        };

        // when
        $result = fn()=>$renderer->renderModel('ModelClass', $setupCallback);

        // then
        $modelFactoryMock->shouldReceive('createModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($modelMock);

        $modelMock->shouldReceive('getTemplateArguments')
            ->once()
            ->andReturn(['key' => 'modified_value']);

        $templateProviderMock->shouldReceive('resolveModelTemplate')
            ->once()
            ->with($modelMock)
            ->andReturn('<div>{{ $key }}</div>');

        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<div>{{ $key }}</div>', ['key' => 'modified_value'], false)
            ->andReturn('<div>modified_value</div>');

        $this->assertSame('<div>modified_value</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderModelPrintsOutput(): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $templateProviderMock = Mockery::mock(ModelTemplateResolverInterface::class);
        $modelMock = Mockery::mock(TemplateModelInterface::class);
        $renderer = new ModelRenderer(
            $templateRendererMock,
            $modelFactoryMock,
            $templateProviderMock
        );

        // when
        $renderModel = fn()=>$renderer->renderModel($modelMock, null, true);

        // then
        $modelMock->shouldReceive('getTemplateArguments')
            ->once()
            ->andReturn(['key' => 'value']);

        $templateProviderMock->shouldReceive('resolveModelTemplate')
            ->once()
            ->with($modelMock)
            ->andReturn('<div>{{ $key }}</div>');

        $templateRendererMock->shouldReceive('renderTemplate')
            ->once()
            ->with('<div>{{ $key }}</div>', ['key' => 'value'], true)
            ->andReturn('<div>value</div>');

        $this->assertSame('<div>value</div>', $renderModel());

        // apply
        Mockery::close();
    }
}
