<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Error;
use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateResolverInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Model\ModelFactory;

class ModelFactoryTest extends TestCase
{
    public function testMakeModelCreatesInstanceOfModelClass(): void
    {
        // given
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);
        $modelTemplateResolverMock = Mockery::mock(ModelTemplateResolverInterface::class);
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);

        $factory = new ModelFactory(
            $objectReaderMock,
            $propertyValueProviderMock,
            $modelTemplateResolverMock,
            $templateRendererMock
        );

        $modelClass = new class (
            $objectReaderMock,
            $propertyValueProviderMock,
            $modelTemplateResolverMock,
            $templateRendererMock
) implements TemplateModelInterface {
            public ObjectReaderInterface $objectReader;
            public PropertyValueProviderInterface $propertyValueProvider;
            public ModelTemplateResolverInterface $modelTemplateResolver;
            public TemplateRendererInterface $templateRenderer;

            public function __construct(
                ObjectReaderInterface $objectReader,
                PropertyValueProviderInterface $propertyValueProvider,
                ModelTemplateResolverInterface $modelTemplateResolver,
                TemplateRendererInterface $templateRenderer
            ) {
                $this->objectReader = $objectReader;
                $this->propertyValueProvider = $propertyValueProvider;
                $this->modelTemplateResolver = $modelTemplateResolver;
                $this->templateRenderer = $templateRenderer;
            }

            public function getTemplateArguments(): array
            {
                return [];
            }
        };

        // when
        $result = $factory->createModel(get_class($modelClass));

        // then
        $this->assertInstanceOf(get_class($modelClass), $result);
        $this->assertSame($objectReaderMock, $result->objectReader);
        $this->assertSame($propertyValueProviderMock, $result->propertyValueProvider);
        $this->assertSame($modelTemplateResolverMock, $result->modelTemplateResolver);
        $this->assertSame($templateRendererMock, $result->templateRenderer);

        // apply
        Mockery::close();
    }

    public function testMakeModelThrowsExceptionForInvalidClass(): void
    {
        // given
        $objectReaderMock = Mockery::mock(ObjectReaderInterface::class);
        $modelTemplateResolverMock = Mockery::mock(ModelTemplateResolverInterface::class);
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);

        $propertyValueProviderMock = Mockery::mock(PropertyValueProviderInterface::class);

        $factory = new ModelFactory(
            $objectReaderMock,
            $propertyValueProviderMock,
            $modelTemplateResolverMock,
            $templateRendererMock
        );

        // when
        $makeModel = fn() => $factory->createModel('NonExistentClass');

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
        $modelTemplateResolverMock = Mockery::mock(ModelTemplateResolverInterface::class);
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);

        $factory = new ModelFactory(
            $objectReaderMock,
            $propertyValueProviderMock,
            $modelTemplateResolverMock,
            $templateRendererMock
        );
        $invalidModelClass = new class (1) {
            public function __construct(int $test)
            {
            }
        };

        // when
        $makeModel = fn() => $factory->createModel(get_class($invalidModelClass));

        // then
        $this->expectException(Error::class);

        $makeModel();
    }
}
