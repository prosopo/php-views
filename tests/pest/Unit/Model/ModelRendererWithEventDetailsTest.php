<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\PrivateClasses\Model\ModelRendererWithEventDetails;

class ModelRendererWithEventDetailsTest extends TestCase
{
    public function testRenderModelAttachesAndDetachesEventDetails(): void
    {
        // given
        $viewRendererMock = Mockery::mock(ModelRendererInterface::class);
        $eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $modelMock = new class {
        };
        $renderer = new ModelRendererWithEventDetails($viewRendererMock, $eventDispatcherMock, 'render_event');

        // when
        $result = fn()=>$renderer->renderModel($modelMock);

        // then
        $eventDispatcherMock->shouldReceive('registerEventDetails')
            ->once()
            ->with('render_event', ['viewClass' => get_class($modelMock)]);

        $viewRendererMock->shouldReceive('renderModel')
            ->once()
            ->with($modelMock, null, false)
            ->andReturn('<div>Rendered View</div>');

        $eventDispatcherMock->shouldReceive('unregisterEventDetails')
            ->once()
            ->with('render_event', ['viewClass' => get_class($modelMock)]);

        $this->assertSame('<div>Rendered View</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderModelHandlesModelClass(): void
    {
        // given
        $viewRendererMock = Mockery::mock(ModelRendererInterface::class);
        $eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $renderer = new ModelRendererWithEventDetails($viewRendererMock, $eventDispatcherMock, 'render_event');

        // when
        $result = fn()=> $renderer->renderModel('TestModelClass');

        // then
        $eventDispatcherMock->shouldReceive('registerEventDetails')
            ->once()
            ->with('render_event', ['viewClass' => 'TestModelClass']);

        $viewRendererMock->shouldReceive('renderModel')
            ->once()
            ->with('TestModelClass', null, false)
            ->andReturn('<div>Rendered Model</div>');

        $eventDispatcherMock->shouldReceive('unregisterEventDetails')
            ->once()
            ->with('render_event', ['viewClass' => 'TestModelClass']);

        $this->assertSame('<div>Rendered Model</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderModelPassesSetupCallback(): void
    {
        // given
        $viewRendererMock = Mockery::mock(ModelRendererInterface::class);
        $eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $modelMock = new class {
            public $key;
        };

        $setupCallback = function ($model) {
            $model->key = 'modified_value';
        };
        $renderer = new ModelRendererWithEventDetails($viewRendererMock, $eventDispatcherMock, 'render_event');

        // when
        $result = fn()=>$renderer->renderModel($modelMock, $setupCallback);

        // then
        $eventDispatcherMock->shouldReceive('registerEventDetails')
            ->once()
            ->with('render_event', ['viewClass' => get_class($modelMock)]);

        $viewRendererMock->shouldReceive('renderModel')
            ->once()
            ->with($modelMock, $setupCallback, false)
            ->andReturn('<div>Modified View</div>');

        $eventDispatcherMock->shouldReceive('unregisterEventDetails')
            ->once()
            ->with('render_event', ['viewClass' => get_class($modelMock)]);

        $this->assertSame('<div>Modified View</div>', $result());

        // apply
        Mockery::close();
    }

    public function testRenderModelHandlesDoPrintFlag(): void
    {
        // given
        $viewRendererMock = Mockery::mock(ModelRendererInterface::class);
        $eventDispatcherMock = Mockery::mock(EventDispatcherInterface::class);
        $modelMock = new class {
        };
        $renderer = new ModelRendererWithEventDetails($viewRendererMock, $eventDispatcherMock, 'render_event');

        // when
        $renderModel = fn()=>$renderer->renderModel($modelMock, null, true);

        // then
        $eventDispatcherMock->shouldReceive('registerEventDetails')
            ->once()
            ->with('render_event', ['viewClass' => get_class($modelMock)]);

        $viewRendererMock->shouldReceive('renderModel')
            ->once()
            ->with($modelMock, null, true)
            ->andReturn('<div>Printed View</div>');

        $eventDispatcherMock->shouldReceive('unregisterEventDetails')
            ->once()
            ->with('render_event', ['viewClass' => get_class($modelMock)]);

        $this->assertSame('<div>Printed View</div>', $renderModel());

        // apply
        Mockery::close();
    }
}
