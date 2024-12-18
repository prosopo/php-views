<?php

declare(strict_types=1);

namespace Tests\Unit\Object;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\Object\PropertyValueProviderForModels;
use Prosopo\Views\BaseTemplateModel;
use ReflectionProperty;

class PropertyValueProviderForModelsTest extends TestCase
{
    public function testSupportsPropertyReturnsTrueForSupportedProperty(): void
    {
        // given
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $propertyMock = Mockery::mock(ReflectionProperty::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($propertyValueProviderMock, $modelFactoryMock);

        // when
        $result = fn() => $provider->supportsProperty($propertyMock);

        // then
        $propertyValueProviderMock->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(true);

        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testSupportsPropertyReturnsTrueForValidModelClass(): void
    {
        // given
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($propertyValueProviderMock, $modelFactoryMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->supportsProperty($propertyMock);

        // then
        $propertyMock->shouldReceive('getType->getName')
            ->once()
            ->andReturn(BaseTemplateModel::class);

        $propertyValueProviderMock->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(false);

        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testSupportsPropertyReturnsFalseForInvalidClass(): void
    {
        // given
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($propertyValueProviderMock, $modelFactoryMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->supportsProperty($propertyMock);

        // then
        $propertyMock->shouldReceive('getType->getName')
            ->once()
            ->andReturn('NonExistentClass');

        $propertyValueProviderMock->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(false);

        $this->assertFalse($result());

        // apply
        Mockery::close();
    }

    public function testGetPropertyValueReturnsFromProvider(): void
    {
        // given
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($propertyValueProviderMock, $modelFactoryMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->getPropertyValue($propertyMock);

        // then
        $propertyValueProviderMock->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(true);

        $propertyValueProviderMock->shouldReceive('getPropertyValue')
            ->once()
            ->with($propertyMock)
            ->andReturn('TestValue');

        $this->assertSame('TestValue', $result());

        // apply
        Mockery::close();
    }

    public function testGetPropertyValueCreatesModelForValidClass(): void
    {
        // given
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($propertyValueProviderMock, $modelFactoryMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);
        $model = Mockery::mock(BaseTemplateModel::class);

        // when
        $result = fn() => $provider->getPropertyValue($propertyMock);

        // then
        $propertyMock->shouldReceive('getType->getName')
            ->once()
            ->andReturn(BaseTemplateModel::class);

        $propertyValueProviderMock->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(false);

        $modelFactoryMock->shouldReceive('createModel')
            ->once()
            ->with(BaseTemplateModel::class)
            ->andReturn($model);

        $this->assertSame($model, $result());

        // apply
        Mockery::close();
    }

    public function testGetPropertyValueReturnsNullForInvalidClass(): void
    {
        // given
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $provider = new PropertyValueProviderForModels($propertyValueProviderMock, $modelFactoryMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->getPropertyValue($propertyMock);

        // then
        $propertyMock->shouldReceive('getType->getName')
            ->once()
            ->andReturn('InvalidClass');

        $propertyValueProviderMock->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(false);

        $this->assertNull($result());

        // apply
        Mockery::close();
    }
}
