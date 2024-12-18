<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelWithDefaultsInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\Model\ModelFactoryWithDefaultsManagement;

class ModelFactoryWithDefaultsManagementTest extends TestCase
{
    public function testMakeModelInitializesPropertiesWhenObjectImplementsWithDefaultsInterface(): void
    {
        // given
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $objectPropertyWriterMock = Mockery::mock(ObjectPropertyWriterInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $modelWithDefaultsMock = Mockery::mock(TemplateModelWithDefaultsInterface::class);
        $factory = new ModelFactoryWithDefaultsManagement(
            $modelFactoryMock,
            $objectReaderMock,
            $objectPropertyWriterMock
        );

        // when
        $result = fn() =>$factory->createModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('createModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($modelWithDefaultsMock);

        $modelWithDefaultsMock
            ->shouldReceive('getDefaultsProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('extractObjectVariables')
            ->once()
            ->with($modelWithDefaultsMock)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('assignPropertyValues')
            ->once()
            ->with($modelWithDefaultsMock, $propertyValueProviderMock);

        $this->assertSame($modelWithDefaultsMock, $result());

        // apply
        Mockery::close();
    }

    public function testMakeModelSkipsPropertiesInitializationWhenObjectWithoutDefaultsInterface(): void
    {
        // given
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $objectPropertyWriterMock = Mockery::mock(ObjectPropertyWriterInterface::class);
        $modelMock = Mockery::mock(TemplateModelInterface::class);
        $factory = new ModelFactoryWithDefaultsManagement(
            $modelFactoryMock,
            $objectReaderMock,
            $objectPropertyWriterMock
        );

        // when
        $result = fn() =>$factory->createModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('createModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($modelMock);

        $this->assertSame($modelMock, $result());

        // apply
        Mockery::close();
    }

    public function testMakeModelHandlesInnerObjectsWithDefaultsInterface(): void
    {
        // given
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $objectPropertyWriterMock = Mockery::mock(ObjectPropertyWriterInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $mainModelMock = Mockery::mock(TemplateModelWithDefaultsInterface::class);
        $innerModelMock1 = Mockery::mock(TemplateModelWithDefaultsInterface::class);
        $innerModelMock2 = Mockery::mock(TemplateModelWithDefaultsInterface::class);
        $factory = new ModelFactoryWithDefaultsManagement(
            $modelFactoryMock,
            $objectReaderMock,
            $objectPropertyWriterMock
        );

        // when
        $result = fn()=>$factory->createModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('createModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($mainModelMock);

        $mainModelMock
            ->shouldReceive('getDefaultsProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('extractObjectVariables')
            ->once()
            ->with($mainModelMock)
            ->andReturn(['inner1' => $innerModelMock1, 'inner2' => $innerModelMock2]);

        $objectPropertyWriterMock->shouldReceive('assignPropertyValues')
            ->once()
            ->with($mainModelMock, $propertyValueProviderMock);

        $innerModelMock1
            ->shouldReceive('getDefaultsProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('extractObjectVariables')
            ->once()
            ->with($innerModelMock1)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('assignPropertyValues')
            ->once()
            ->with($innerModelMock1, $propertyValueProviderMock);

        $innerModelMock2
            ->shouldReceive('getDefaultsProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('extractObjectVariables')
            ->once()
            ->with($innerModelMock2)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('assignPropertyValues')
            ->once()
            ->with($innerModelMock2, $propertyValueProviderMock);

        $this->assertSame($mainModelMock, $result());

        // apply
        Mockery::close();
    }

    public function testMakeModelSkipsInnerObjectsWithoutDefaultsInterface(): void
    {
        // given
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $objectPropertyWriterMock = Mockery::mock(ObjectPropertyWriterInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $mainModelMock = Mockery::mock(TemplateModelWithDefaultsInterface::class);
        $innerModelMock1 = Mockery::mock(TemplateModelInterface::class);
        $factory = new ModelFactoryWithDefaultsManagement(
            $modelFactoryMock,
            $objectReaderMock,
            $objectPropertyWriterMock
        );

        // when
        $result = fn()=>$factory->createModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('createModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($mainModelMock);

        $mainModelMock
            ->shouldReceive('getDefaultsProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('extractObjectVariables')
            ->once()
            ->with($mainModelMock)
            ->andReturn(['inner1' => $innerModelMock1,]);

        $objectPropertyWriterMock->shouldReceive('assignPropertyValues')
            ->once()
            ->with($mainModelMock, $propertyValueProviderMock);

        $this->assertSame($mainModelMock, $result());

        // apply
        Mockery::close();
    }
}
