<?php

declare(strict_types=1);

namespace Tests\Unit\ObjectProperty;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\ObjectProperty\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\ObjectProperty\PropertyValueProvider;

class PropertyValueProviderTest extends TestCase
{
    public function testSupportsReturnsTrueForTypeInDefaultValues(): void
    {
        // given
        $defaultValues = ['string' => 'Default String Value'];
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProvider($instanceProvider, $defaultValues);

        // when
        $result = fn() => $provider->supports('string');

        // then
        $instanceProvider->shouldNotReceive('supports');
        $this->assertTrue($result());

        // apply
        Mockery::close();
    }

    public function testSupportsDelegatesToInstanceProvider(): void
    {
        // given
        $defaultValues = [];
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProvider($instanceProvider, $defaultValues);

        // when
        $result = fn() => $provider->supports('customType');

        // then
        $instanceProvider->shouldReceive('supports')
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
        $provider = new PropertyValueProvider($instanceProvider, $defaultValues);

        // when
        $result = fn() => $provider->getValue('string');

        // then
        $instanceProvider->shouldNotReceive('getValue'); // No need to call instance provider
        $this->assertSame('Default String Value', $result());

        // apply
        Mockery::close();
    }

    public function testGetValueDelegatesToInstanceProvider(): void
    {
        // given
        $instanceProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $provider = new PropertyValueProvider($instanceProvider, []);

        // when
        $result = fn() => $provider->getValue('customType');

        // then
        $instanceProvider->shouldReceive('getValue')
            ->once()
            ->with('customType')
            ->andReturn('Instance Provided Value');
        $this->assertSame('Instance Provided Value', $result());

        // apply
        Mockery::close();
    }
}
