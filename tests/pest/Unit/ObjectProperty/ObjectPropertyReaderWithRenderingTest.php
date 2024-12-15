<?php

declare(strict_types=1);

namespace Tests\Unit\ObjectProperty;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\ObjectProperty\ObjectPropertyReaderInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;
use Prosopo\Views\Interfaces\View\ViewRendererInterface;
use Prosopo\Views\PrivateClasses\ObjectProperty\ObjectPropertyReaderWithRendering;

class ObjectPropertyReaderWithRenderingTest extends TestCase
{
    public function testGetVariablesRendersInnerViews(): void
    {
        // given
        $objectPropertyReader = Mockery::mock(ObjectPropertyReaderInterface::class);
        $viewRenderer = Mockery::mock(ViewRendererInterface::class);
        $reader = new ObjectPropertyReaderWithRendering($objectPropertyReader, $viewRenderer);
        $viewInstance = Mockery::mock(ViewInterface::class);

        // when
        $result = fn() => $reader->getVariables(new class {
        });

        // then
        $objectPropertyReader->shouldReceive('getVariables')
            ->once()
            ->with(Mockery::type('object'))
            ->andReturn([
                'key1' => 'value1',
                'key2' => $viewInstance,
                'key3' => ['nestedKey' => $viewInstance],
            ]);
        $viewRenderer->shouldReceive('renderView')
            ->twice()
            ->with($viewInstance)
            ->andReturn('<div>Rendered View</div>');

        $this->assertEquals([
            'key1' => 'value1',
            'key2' => '<div>Rendered View</div>',
            'key3' => ['nestedKey' => '<div>Rendered View</div>'],
        ], $result());

        // apply
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }

    public function testGetVariablesIgnoresNonViewItems(): void
    {
        // given
        $objectPropertyReader = Mockery::mock(ObjectPropertyReaderInterface::class);
        $viewRenderer = Mockery::mock(ViewRendererInterface::class);
        $reader = new ObjectPropertyReaderWithRendering($objectPropertyReader, $viewRenderer);

        // when
        $result = fn() => $reader->getVariables(new class {
        });

        // then
        $objectPropertyReader->shouldReceive('getVariables')
            ->once()
            ->with(Mockery::type('object'))
            ->andReturn([
                'key1' => 'value1',
                'key2' => 123,
                'key3' => ['nestedKey' => 'string'],
            ]);
        $viewRenderer->shouldNotReceive('renderView');

        $this->assertEquals([
            'key1' => 'value1',
            'key2' => 123,
            'key3' => ['nestedKey' => 'string'],
        ], $result());

        // apply
        $this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
        Mockery::close();
    }
}
