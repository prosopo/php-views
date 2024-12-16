<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Error;
use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\PrivateClasses\Model\ModelFactory;

class ModelFactoryTest extends TestCase
{
    public function testMakeModelCreatesInstanceOfModelClass(): void
    {
        // given
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $factory = new ModelFactory($objectReaderMock);

        $modelClass = new class ($objectReaderMock) implements TemplateModelInterface {
            private ObjectReaderInterface $objectReader;

            public function __construct(ObjectReaderInterface $objectReader)
            {
                $this->objectReader = $objectReader;
            }

            public function getObjectReader(): ObjectReaderInterface
            {
                return $this->objectReader;
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
        $this->assertSame($objectReaderMock, $result->getObjectReader());

        // apply
        Mockery::close();
    }

    public function testMakeModelThrowsExceptionForInvalidClass(): void
    {
        // given
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $factory = new ModelFactory($objectReaderMock);

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
        $factory = new ModelFactory($objectReaderMock);
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
