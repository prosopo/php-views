<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\PrivateClasses\Object\ObjectClassReader;
use Prosopo\Views\PrivateClasses\Template\FileModelTemplateProvider;

class FileModelTemplateProviderTest extends TestCase
{
    public function testGetTemplateReturnsFileContentForValidView(): void
    {
        // given
         vfsStream::setup('templates', null, ['sample-view.blade.php' => 'View Content']);
         $templateModel = Mockery::mock(TemplateModelInterface::class);
         $classReaderMock = Mockery::mock(ObjectClassReader::class);
         $provider = new FileModelTemplateProvider(
             vfsStream::url('templates'),
             'App\\Views',
             '.blade.php',
             $classReaderMock
         );

        // when
        $result = fn() => $provider->getModelTemplate($templateModel);

        // then
        $classReaderMock->shouldReceive('getObjectClass')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views\\SampleView');

        $this->assertSame('View Content', $result());

        // apply
        Mockery::close();
    }

    public function testGetTemplateReturnsEmptyStringForMissingFile(): void
    {
        // given
        vfsStream::setup('templates');
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $classReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new FileModelTemplateProvider(
            vfsStream::url('templates'),
            'App\\Views',
            '.blade.php',
            $classReaderMock
        );

        // when
        $result = fn() => $provider->getModelTemplate($templateModel);

        // then
        $classReaderMock->shouldReceive('getObjectClass')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views\\SampleView');

        $this->assertSame('', $result());

        // apply
        Mockery::close();
    }

    public function testGetTemplateHandlesCamelCaseConversion(): void
    {
        // given
        vfsStream::setup('templates', null, ['some-camel-case-view.blade.php' => 'Camel Case Content']);
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $classReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new FileModelTemplateProvider(
            vfsStream::url('templates'),
            'App\\Views',
            '.blade.php',
            $classReaderMock
        );

        // when
        $result = fn() => $provider->getModelTemplate($templateModel);

        // then
        $classReaderMock->shouldReceive('getObjectClass')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views\\SomeCamelCaseView');

        $this->assertSame('Camel Case Content', $result());

        // apply
        Mockery::close();
    }

    public function testGetTemplateHandlesNestedNamespaces(): void
    {
        // given
        vfsStream::setup('templates', null, [
            'admin/dashboard-view.blade.php' => 'Dashboard Content'
        ]);
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $classReaderMock = Mockery::mock(ObjectClassReader::class);
        $provider = new FileModelTemplateProvider(
            vfsStream::url('templates'),
            'App\\Views',
            '.blade.php',
            $classReaderMock
        );

        // when
        $result = fn() => $provider->getModelTemplate($templateModel);

        // then
        $classReaderMock->shouldReceive('getObjectClass')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views\\Admin\\DashboardView');

        $this->assertSame('Dashboard Content', $result());

        // apply
        Mockery::close();
    }
}
