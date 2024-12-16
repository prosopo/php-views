<?php

declare(strict_types=1);

namespace Tests\Unit\Object;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\Object\PropertyValueProviderByTypes;
use ReflectionNamedType;
use ReflectionProperty;

class PropertyValueProviderByTypesTest extends TestCase
{
    public function testSupportsPropertyReturnsTrueForTypeInDefaultValues(): void
    {
        // given
        $defaultValues = ['string' => 'Default String Value'];
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, $defaultValues);
        $propertyMock = Mockery::mock(ReflectionProperty::class);
        $typeMock = Mockery::mock(ReflectionNamedType::class);

        // when
        $result = fn() => $provider->supportsProperty($propertyMock);

        // then
        $instanceProvider->shouldReceive('supportsProperty')
            ->once()
            ->andReturn(false);

        $propertyMock->shouldReceive('getType')
            ->once()
            ->andReturn($typeMock);

        $typeMock->shouldReceive('getName')
            ->once()
            ->andReturn('string');

        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testSupportsPropertyDelegatesToInstanceProvider(): void
    {
        // given
        $defaultValues = [];
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, $defaultValues);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->supportsProperty($propertyMock);

        // then
        $instanceProvider->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(true);

        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testGetPropertyValueReturnsDefaultValue(): void
    {
        // given
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $propertyMock = Mockery::mock(ReflectionProperty::class);
        $typeMock = Mockery::mock(ReflectionNamedType::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, ['string' => 'Default String Value']);

        // when
        $result = fn() => $provider->getPropertyValue($propertyMock);

        // then
        $instanceProvider->shouldReceive('supportsProperty')
            ->once()
            ->andReturn(false);

        $propertyMock->shouldReceive('getType')
            ->once()
            ->andReturn($typeMock);

        $typeMock->shouldReceive('getName')
            ->once()
            ->andReturn('string');

        $instanceProvider->shouldNotReceive('getPropertyValue');

        $this->assertSame('Default String Value', $result());

        // apply
        Mockery::close();
    }

    public function testGetPropertyValueDelegatesToInstanceProvider(): void
    {
        // given
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, []);
        $propertyMock = Mockery::mock(ReflectionProperty::class);

        // when
        $result = fn() => $provider->getPropertyValue($propertyMock);

        // then
        $instanceProvider->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(true);

        $instanceProvider->shouldReceive('getPropertyValue')
            ->once()
            ->with($propertyMock)
            ->andReturn('Instance Provided Value');

        $this->assertSame('Instance Provided Value', $result());

        // apply
        Mockery::close();
    }

    public function testGetPropertyValueReturnsNullForUnsupportedType(): void
    {
        // given
        $defaultValues = [];
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, $defaultValues);
        $propertyMock = Mockery::mock(ReflectionProperty::class);
        $typeMock = Mockery::mock(ReflectionNamedType::class);

        // when
        $result = fn() => $provider->getPropertyValue($propertyMock);

        // then
        $propertyMock->shouldReceive('getType')
            ->once()
            ->andReturn($typeMock);

        $typeMock->shouldReceive('getName')
            ->once()
            ->andReturn('unsupportedType');

        $instanceProvider->shouldReceive('supportsProperty')
            ->once()
            ->with($propertyMock)
            ->andReturn(false);

        $this->assertNull($result());

        // apply
        Mockery::close();
    }
}
