<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Error;
use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\PrivateClasses\Model\ModelFactory;

class ModelFactoryTest extends TestCase
{
    public function testMakeModelCreatesInstanceOfModelClass(): void
    {
        // given
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $factory = new ModelFactory($objectReaderMock, $propertyValueProviderMock);

        $modelClass = new class ($objectReaderMock, $propertyValueProviderMock) implements TemplateModelInterface {
            public ObjectReaderInterface $objectReader;
            public PropertyValueProviderInterface $propertyValueProvider;

            public function __construct(
                ObjectReaderInterface $objectReader,
                PropertyValueProviderInterface $propertyValueProvider
            ) {
                $this->objectReader = $objectReader;
                $this->propertyValueProvider = $propertyValueProvider;
            }

            public function getTemplateArguments(): array
            {
                return [];
            }
        };

        // when
        $result = $factory->makeModel(get_class($modelClass));

        // then
        $this->assertInstanceOf(get_class($modelClass), $result);
        $this->assertSame($objectReaderMock, $result->objectReader);
        $this->assertSame($propertyValueProviderMock, $result->propertyValueProvider);

        // apply
        Mockery::close();
    }

    public function testMakeModelThrowsExceptionForInvalidClass(): void
    {
        // given
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $factory = new ModelFactory($objectReaderMock, $propertyValueProviderMock);

        // when
        $makeModel = fn() => $factory->makeModel('NonExistentClass');

        // when & then
        $this->expectException(Error::class);

        $this->expectExceptionMessage("Class \"NonExistentClass\" not found");

        $makeModel();
    }

    public function testMakeModelThrowsExceptionForInvalidConstructor(): void
    {
        // given
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $factory = new ModelFactory($objectReaderMock, $propertyValueProviderMock);
        $invalidModelClass = new class (1) {
            public function __construct(int $test)
            {
            }
        };

        // when
        $makeModel = fn() => $factory->makeModel(get_class($invalidModelClass));

        // then
        $this->expectException(Error::class);

        $makeModel();
    }
}
