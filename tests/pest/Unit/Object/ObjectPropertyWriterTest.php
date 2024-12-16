<?php

declare(strict_types=1);

namespace Tests\Unit\Object;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\Object\ObjectPropertyWriter;

class ObjectPropertyWriterTest extends TestCase
{
    public function testSetsDefaultValuesUsingProvider(): void
    {
        // given
        $propertyValueProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $testInstance = new class {
            public string $name;
        };
        $writer = new ObjectPropertyWriter();

        // when
        $result = fn() => $writer->setObjectPropertyValues($testInstance, $propertyValueProvider);

        // then
        $propertyValueProvider->shouldReceive('supportsProperty')
            ->once()
            ->andReturn(true);

        $propertyValueProvider->shouldReceive('getPropertyValue')
            ->once()
            ->andReturn('Default Name');

        $result();

        $this->assertSame('Default Name', $testInstance->name);

        // apply
        Mockery::close();
    }

    public function testSetsNullForNullableProperty(): void
    {
        // given
        $writer = new ObjectPropertyWriter();

        $testInstance = new class {
            public ?string $nullableName; // Nullable and uninitialized
        };

        // when
        $result = fn() => $writer->setObjectPropertyValues($testInstance);

        // then
        $result();

        $this->assertNull($testInstance->nullableName);
    }

    public function testIgnoresInitializedProperties(): void
    {
        // given
        $writer = new ObjectPropertyWriter();

        $testInstance = new class {
            public string $name = 'Initialized Name';
        };

        // when
        $result = fn() => $writer->setObjectPropertyValues($testInstance);

        // then
        $result();

        $this->assertSame('Initialized Name', $testInstance->name);
    }

    public function testSkipsNonSupportedType(): void
    {
        // given
        $propertyValueProvider = Mockery::mock(PropertyValueProviderInterface::class);
        $testInstance = new class {
            public int $age;
        };
        $writer = new ObjectPropertyWriter();

        // when
        $result = fn() => $writer->setObjectPropertyValues($testInstance, $propertyValueProvider);

        // then
        $propertyValueProvider->shouldReceive('supportsProperty')
            ->once()
            ->andReturn(false);

        $result();

        $this->assertFalse(isset($testInstance->age));

        // apply
        Mockery::close();
    }
}