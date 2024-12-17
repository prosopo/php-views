<?php

declare(strict_types=1);

namespace Tests\Unit\Object;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\Object\PropertyValueProviderForNullable;
use ReflectionProperty;
use ReflectionType;

class PropertyValueProviderForNullableTest extends TestCase
{
    public function testSupportsPropertyDelegatesToInnerProvider(): void
    {
        // given
        $innerProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderForNullable($innerProviderMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->supportsProperty($propertyMock);

        // then
        $innerProviderMock->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(true);

        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testSupportsPropertyReturnsTrueForNullableProperty(): void
    {
        // given
        $innerProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderForNullable($innerProviderMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);
        $reflectionTypeMock = Mockery::mock(ReflectionType::class);

        // when
        $result = fn()=> $provider->supportsProperty($propertyMock);

        // then
        $innerProviderMock
            ->shouldReceive('supportsProperty')
        ->once()
        ->andReturn(false);

        $propertyMock
            ->shouldReceive('getType')
            ->once()
            ->andReturn($reflectionTypeMock);

        $reflectionTypeMock
            ->shouldReceive('allowsNull')
            ->once()
            ->andReturn(true);

        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testSupportsPropertyReturnsFalseForNonNullableProperty(): void
    {
        // given
        $innerProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderForNullable($innerProviderMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);
        $reflectionTypeMock = Mockery::mock(ReflectionType::class);

        // when
        $result = fn()=> $provider->supportsProperty($propertyMock);

        // then
        $innerProviderMock
            ->shouldReceive('supportsProperty')
            ->once()
            ->andReturn(false);

        $propertyMock
            ->shouldReceive('getType')
            ->once()
            ->andReturn($reflectionTypeMock);

        $reflectionTypeMock
            ->shouldReceive('allowsNull')
            ->once()
            ->andReturn(false);

        $this->assertFalse($result());

        // apply
        Mockery::close();
    }

    public function testGetPropertyValueDelegatesToInnerProvider(): void
    {
        // given
        $innerProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderForNullable($innerProviderMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->getPropertyValue($propertyMock);

        // then
        $innerProviderMock
            ->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(true);

        $innerProviderMock
            ->shouldReceive('getPropertyValue')
            ->once()
            ->with($propertyMock)
            ->andReturn('value');

        $this->assertSame('value', $result());

        // apply
        Mockery::close();
    }

    public function testGetPropertyValueReturnsNullForNullableProperty(): void
    {
        // given
        $innerProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderForNullable($innerProviderMock);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->getPropertyValue($propertyMock);

        // then
        $innerProviderMock
            ->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(false);

        $this->assertNull($result());

        // apply
        Mockery::close();
    }
}
