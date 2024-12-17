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
        $result = fn() =>$factory->makeModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('makeModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($modelWithDefaultsMock);

        $modelWithDefaultsMock
            ->shouldReceive('getDefaultsPropertyValueProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($modelWithDefaultsMock)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
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
        $result = fn() =>$factory->makeModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('makeModel')
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
        $result = fn()=>$factory->makeModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('makeModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($mainModelMock);

        $mainModelMock
            ->shouldReceive('getDefaultsPropertyValueProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($mainModelMock)
            ->andReturn(['inner1' => $innerModelMock1, 'inner2' => $innerModelMock2]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($mainModelMock, $propertyValueProviderMock);

        $innerModelMock1
            ->shouldReceive('getDefaultsPropertyValueProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($innerModelMock1)
            ->andReturn([]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($innerModelMock1, $propertyValueProviderMock);

        $innerModelMock2
            ->shouldReceive('getDefaultsPropertyValueProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

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
        $result = fn()=>$factory->makeModel('ModelClass');

        // then
        $modelFactoryMock->shouldReceive('makeModel')
            ->once()
            ->with('ModelClass')
            ->andReturn($mainModelMock);

        $mainModelMock
            ->shouldReceive('getDefaultsPropertyValueProvider')
            ->once()
            ->andReturn($propertyValueProviderMock);

        $objectReaderMock->shouldReceive('getObjectVariables')
            ->once()
            ->with($mainModelMock)
            ->andReturn(['inner1' => $innerModelMock1,]);

        $objectPropertyWriterMock->shouldReceive('setObjectPropertyValues')
            ->once()
            ->with($mainModelMock, $propertyValueProviderMock);

        $this->assertSame($mainModelMock, $result());

        // apply
        Mockery::close();
    }
}
