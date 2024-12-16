<?php

declare(strict_types=1);

namespace Tests\Unit\Object;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\PrivateClasses\Object\ObjectReaderWithRendering;

class ObjectReaderWithRenderingTest extends TestCase
{
    public function testGetVariablesRendersInnerViews(): void
    {
        // given
        $objectReader = Mockery::mock(ObjectReaderInterface::class);
        $modelRenderer = Mockery::mock(ModelRendererInterface::class);
        $reader = new ObjectReaderWithRendering($objectReader, $modelRenderer);
        $model = Mockery::mock(TemplateModelInterface::class);

        // when
        $result = fn() => $reader->getObjectVariables(new class {
        });

        // then
        $objectReader->shouldReceive('getObjectVariables')
            ->once()
            ->with(Mockery::type('object'))
            ->andReturn([
                'key1' => 'value1',
                'key2' => $model,
                'key3' => ['nestedKey' => $model],
            ]);
        $modelRenderer->shouldReceive('renderModel')
            ->twice()
            ->with($model)
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
        $objectReader = Mockery::mock(ObjectReaderInterface::class);
        $modelRenderer = Mockery::mock(ModelRendererInterface::class);
        $reader = new ObjectReaderWithRendering($objectReader, $modelRenderer);

        // when
        $result = fn() => $reader->getObjectVariables(new class {
        });

        // then
        $objectReader->shouldReceive('getObjectVariables')
            ->once()
            ->with(Mockery::type('object'))
            ->andReturn([
                'key1' => 'value1',
                'key2' => 123,
                'key3' => ['nestedKey' => 'string'],
            ]);
        $modelRenderer->shouldNotReceive('renderModel');

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
