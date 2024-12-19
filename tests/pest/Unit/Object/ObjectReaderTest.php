<?php

declare(strict_types=1);

namespace Tests\Unit\Object;

use PHPUnit\Framework\TestCase;
use Prosopo\Views\PrivateClasses\Object\ObjectReader;

class ObjectReaderTest extends TestCase
{
    public function testReturnsPublicTypedPropertiesAndMethods(): void
    {
        // given
        $objectReader = new ObjectReader();
        $testInstance = new class {
            public string $name = 'John Doe';
            public int $age = 30;

            public function greet(): string
            {
                return 'Hello';
            }

            public function calculate(): int
            {
                return 42;
            }
        };

        // when
        $variables = $objectReader->extractObjectVariables($testInstance);

        // then
        $this->assertEquals([
            'age' => 30,
            'calculate' => [$testInstance, 'calculate'],
            'greet' => [$testInstance, 'greet'],
            'name' => 'John Doe',
        ], $variables);
    }

    public function testExcludesNonTypedPublicProperties(): void
    {
        // given
        $objectReader = new ObjectReader();
        $testInstance = new class {
            public $nonTypedProperty = 'value';
            public int $typedProperty = 42;
        };

        // when
        $variables = $objectReader->extractObjectVariables($testInstance);

        // then
        $this->assertEquals([
            'typedProperty' => 42,
        ], $variables);
    }

    public function testExcludesConstructorMethod(): void
    {
        // given
        $objectReader = new ObjectReader();
        $testInstance = new class {
            public string $data = 'sample';

            public function someMethod(): string
            {
                return 'test';
            }
        };

        // when
        $variables = $objectReader->extractObjectVariables($testInstance);

        // then

        $this->assertEquals([
            'data' => 'sample',
            'someMethod' => [$testInstance, 'someMethod'],
        ], $variables);
    }

    public function testExcludesNotInitializedProperties(): void
    {
        // given
        $objectReader = new ObjectReader();
        $testInstance = new class {
            public int $typedProperty = 1;
            public int $typedButNotInitializedProperty;
        };

        // when
        $variables = $objectReader->extractObjectVariables($testInstance);

        // then
        $this->assertEquals([
            'typedProperty' => 1,
        ], $variables);
    }
}
