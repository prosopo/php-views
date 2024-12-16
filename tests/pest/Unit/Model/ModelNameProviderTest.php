<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\PrivateClasses\Model\ModelNameProvider;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;

class ModelNameProviderTest extends TestCase
{
    public function testGetModelNameReturnsClassName(): void
    {
        // given
        $objectClassReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new ModelNameProvider($objectClassReaderMock);
        $modelMock = Mockery::mock(TemplateModelInterface::class);

        // when
        $getModelName = fn()=> $provider->getModelName($modelMock);

        // then
        $objectClassReaderMock
            ->shouldReceive('getObjectClass')
            ->andReturn('App\\Models\\Model');

        $this->assertSame('Model', $getModelName());

        // apply
        Mockery::close();
    }

    public function testGetModelNameReturnsClassNameForRoot(): void
    {
        // given
        $objectClassReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new ModelNameProvider($objectClassReaderMock);
        $modelMock = Mockery::mock(TemplateModelInterface::class);

        // when
        $getModelNamespace = fn()=> $provider->getModelName($modelMock);

        // then
        $objectClassReaderMock
            ->shouldReceive('getObjectClass')
            ->andReturn('Model');

        $this->assertSame('', $getModelNamespace());

        // apply
        Mockery::close();
    }

    public function testGetModelNameReturnsClassNameForDeeplyNested(): void
    {
        // given
        $objectClassReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new ModelNameProvider($objectClassReaderMock);
        $modelMock = Mockery::mock(TemplateModelInterface::class);

        // when
        $getModelNamespace = fn()=> $provider->getModelName($modelMock);

        // then
        $objectClassReaderMock
            ->shouldReceive('getObjectClass')
            ->andReturn('App\\Models\\And\\Several\\Levels\\Inside\\Model');

        $this->assertSame('Model', $getModelNamespace());

        // apply
        Mockery::close();
    }
}
