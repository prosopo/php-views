<?php

declare(strict_types=1);

namespace Tests\Unit\Model;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\PrivateClasses\Model\ModelNamespaceResolver;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;

class ModelNamespaceProviderTest extends TestCase
{
    public function testGetModelNamespaceReturnsNamespace(): void
    {
        // given
        $objectClassReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new ModelNamespaceResolver($objectClassReaderMock);
        $modelMock = Mockery::mock(TemplateModelInterface::class);

        // when
        $getModelNamespace = fn()=> $provider->resolveModelNamespace($modelMock);

        // then
        $objectClassReaderMock
            ->shouldReceive('getObjectClass')
            ->andReturn('App\\Models\\Model');

        $this->assertSame('App\\Models', $getModelNamespace());

        // apply
        Mockery::close();
    }

    public function testGetModelNamespaceReturnsNamespaceForRoot(): void
    {
        // given
        $objectClassReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new ModelNamespaceResolver($objectClassReaderMock);
        $modelMock = Mockery::mock(TemplateModelInterface::class);

        // when
        $getModelNamespace = fn()=> $provider->resolveModelNamespace($modelMock);

        // then
        $objectClassReaderMock
            ->shouldReceive('getObjectClass')
            ->andReturn('Model');

        $this->assertSame('', $getModelNamespace());

        // apply
        Mockery::close();
    }

    public function testGetModelNamespaceReturnsNamespaceForDeeplyNested(): void
    {
        // given
        $objectClassReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new ModelNamespaceResolver($objectClassReaderMock);
        $modelMock = Mockery::mock(TemplateModelInterface::class);

        // when
        $getModelNamespace = fn()=> $provider->resolveModelNamespace($modelMock);

        // then
        $objectClassReaderMock
            ->shouldReceive('getObjectClass')
            ->andReturn('App\\Models\\And\\Several\\Levels\\Inside\\Model');

        $this->assertSame('App\\Models\\And\\Several\\Levels\\Inside', $getModelNamespace());

        // apply
        Mockery::close();
    }
}
