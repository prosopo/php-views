<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\Model\ModelFactoryWithPropertyInitialization;
use stdClass;

class ModelFactoryWithPropertyInitializationTest extends TestCase
{
    public function testMakeModelInitializesProperties(): void
    {
        // given
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $objectPropertyWriterMock = Mockery::mock(ObjectPropertyWriterInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $modelMock = Mockery::mock(TemplateModelInterface::class);
        $factory = new ModelFactoryWithPropertyInitialization(
            $modelFactoryMock,
            $objectReaderMock,
            $objectPropertyWriterMock,
            $propertyValueProviderMock
        );

        // when
        $result = fn() =>$factory->makeModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('makeModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($modelMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($modelMock)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($modelMock, $propertyValueProviderMock);

        $this->assertSame($modelMock, $result());

        // apply
        Mockery::close();
    }

    public function testMakeModelHandlesInnerModels(): void
    {
        // given
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $objectPropertyWriterMock = Mockery::mock(ObjectPropertyWriterInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $mainModelMock = Mockery::mock(TemplateModelInterface::class);
        $innerModelMock1 = Mockery::mock(TemplateModelInterface::class);
        $innerModelMock2 = Mockery::mock(TemplateModelInterface::class);
        $factory = new ModelFactoryWithPropertyInitialization(
            $modelFactoryMock,
            $objectReaderMock,
            $objectPropertyWriterMock,
            $propertyValueProviderMock
        );

        // when
        $result = fn()=>$factory->makeModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('makeModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($mainModelMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($mainModelMock)
            ->andReturn(['inner1' => $innerModelMock1, 'inner2' => $innerModelMock2]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($mainModelMock, $propertyValueProviderMock);


        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($innerModelMock1)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($innerModelMock1, $propertyValueProviderMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($innerModelMock2)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($innerModelMock2, $propertyValueProviderMock);

        $this->assertSame($mainModelMock, $result());

        // apply
        Mockery::close();
    }

    public function testMakeModelSkipsNonModelInners(): void
    {
        // given
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $objectPropertyWriterMock = Mockery::mock(ObjectPropertyWriterInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $mainModelMock = Mockery::mock(TemplateModelInterface::class);
        $innerModelMock = Mockery::mock(TemplateModelInterface::class);
        $nonModelMock = new stdClass();
        $factory = new ModelFactoryWithPropertyInitialization(
            $modelFactoryMock,
            $objectReaderMock,
            $objectPropertyWriterMock,
            $propertyValueProviderMock
        );

        // when
        $result = fn()=>$factory->makeModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('makeModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($mainModelMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($mainModelMock)
            ->andReturn(['inner1' => $innerModelMock, 'inner2' => $nonModelMock]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($mainModelMock, $propertyValueProviderMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($innerModelMock)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($innerModelMock, $propertyValueProviderMock);

        $this->assertSame($mainModelMock, $result());

        // apply
        Mockery::close();
    }
}
