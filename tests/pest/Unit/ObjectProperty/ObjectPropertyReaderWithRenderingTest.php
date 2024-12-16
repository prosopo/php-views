<?php

declare(strict_types=1);

namespace Tests\Unit\ObjectProperty;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\View\ViewInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\PrivateClasses\Object\ObjectReaderWithRendering;

class ObjectPropertyReaderWithRenderingTest extends TestCase
{
    public function testGetVariablesRendersInnerViews(): void
    {
        // given
        $objectPropertyReader = Mockery::mock(ObjectReaderInterface::class);
        $viewRenderer = Mockery::mock(ModelRendererInterface::class);
        $reader = new ObjectReaderWithRendering($objectPropertyReader, $viewRenderer);
        $viewInstance = Mockery::mock(ViewInterface::class);

        // when
        $result = fn() => $reader->getObjectVariables(new class {
        });

        // then
        $objectPropertyReader->shouldReceive('getObjectVariables')
            ->once()
            ->with(Mockery::type('object'))
            ->andReturn([
                'key1' => 'value1',
                'key2' => $viewInstance,
                'key3' => ['nestedKey' => $viewInstance],
            ]);
        $viewRenderer->shouldReceive('renderModel')
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
        $objectPropertyReader = Mockery::mock(ObjectReaderInterface::class);
        $viewRenderer = Mockery::mock(ModelRendererInterface::class);
        $reader = new ObjectReaderWithRendering($objectPropertyReader, $viewRenderer);

        // when
        $result = fn() => $reader->getObjectVariables(new class {
        });

        // then
        $objectPropertyReader->shouldReceive('getObjectVariables')
            ->once()
            ->with(Mockery::type('object'))
            ->andReturn([
                'key1' => 'value1',
                'key2' => 123,
                'key3' => ['nestedKey' => 'string'],
            ]);
        $viewRenderer->shouldNotReceive('renderModel');

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
