<?php

declare(strict_types=1);

namespace Tests\Unit\ObjectProperty;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\Object\PropertyValueProviderByTypes;

class PropertyValueProviderTest extends TestCase
{
    public function testSupportsReturnsTrueForTypeInDefaultValues(): void
    {
        // given
        $defaultValues = ['string' => 'Default String Value'];
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, $defaultValues);

        // when
        $result = fn() => $provider->supportsProperty('string');

        // then
        $instanceProvider->shouldNotReceive('supportsProperty');
        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testSupportsDelegatesToInstanceProvider(): void
    {
        // given
        $defaultValues = [];
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, $defaultValues);

        // when
        $result = fn() => $provider->supportsProperty('customType');

        // then
        $instanceProvider->shouldReceive('supportsProperty')
            ->once()
            ->with('customType')
            ->andReturn(true);

        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testGetValueReturnsDefaultValue(): void
    {
        // given
        $defaultValues = ['string' => 'Default String Value'];
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, $defaultValues);

        // when
        $result = fn() => $provider->getPropertyValue('string');

        // then
        $instanceProvider->shouldNotReceive('getPropertyValue'); // No need to call instance provider
        $this->assertSame('Default String Value', $result());

        // apply
        Mockery::close();
    }

    public function testGetValueDelegatesToInstanceProvider(): void
    {
        // given
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProviderByTypes($instanceProvider, []);

        // when
        $result = fn() => $provider->getPropertyValue('customType');

        // then
        $instanceProvider->shouldReceive('getPropertyValue')
            ->once()
            ->with('customType')
            ->andReturn('Instance Provided Value');
        $this->assertSame('Instance Provided Value', $result());

        // apply
        Mockery::close();
    }
}
