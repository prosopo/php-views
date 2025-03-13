<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use Mockery;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\Model\ModelNameResolverInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceResolverInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\PrivateClasses\Template\FileModelTemplateResolver;

class FileModelTemplateProviderTest extends TestCase
{
    public function testGetTemplateReturnsFileContent(): void
    {
        // given
        vfsStream::setup('templates', null, ['sample-view.blade.php' => 'View Content']);
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $modelNamespaceProviderMock = Mockery::mock(ModelNamespaceResolverInterface::class);
        $modelNameProviderMock = Mockery::mock(ModelNameResolverInterface::class);
        $provider = new FileModelTemplateResolver(
            'App\\Views',
            vfsStream::url('templates'),
            '.blade.php',
            false,
            $modelNamespaceProviderMock,
            $modelNameProviderMock
        );

        // when
        $result = fn() => $provider->resolveModelTemplate($templateModel);

        // then
        $modelNamespaceProviderMock->shouldReceive('resolveModelNamespace')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views');
        $modelNameProviderMock->shouldReceive('resolveModelName')
            ->once()
            ->with($templateModel)
            ->andReturn('SampleView');

        $this->assertSame('View Content', $result());

        // apply
        Mockery::close();
    }

    public function testGetFileBasedTemplateReturnsFileName(): void
    {
        // given
        vfsStream::setup('templates', null, ['sample-view.blade.php' => 'View Content']);
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $modelNamespaceProviderMock = Mockery::mock(ModelNamespaceResolverInterface::class);
        $modelNameProviderMock = Mockery::mock(ModelNameResolverInterface::class);
        $provider = new FileModelTemplateResolver(
            'App\\Views',
            vfsStream::url('templates'),
            '.blade.php',
            true,
            $modelNamespaceProviderMock,
            $modelNameProviderMock
        );

        // when
        $result = fn() => $provider->resolveModelTemplate($templateModel);

        // then
        $modelNamespaceProviderMock->shouldReceive('resolveModelNamespace')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views');
        $modelNameProviderMock->shouldReceive('resolveModelName')
            ->once()
            ->with($templateModel)
            ->andReturn('SampleView');

        $this->assertSame(vfsStream::url('templates/sample-view.blade.php'), $result());

        // apply
        Mockery::close();
    }

    public function testGetTemplateReturnsEmptyStringForMissingFile(): void
    {
        // given
        vfsStream::setup('templates');
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $modelNamespaceProviderMock = Mockery::mock(ModelNamespaceResolverInterface::class);
        $modelNameProviderMock = Mockery::mock(ModelNameResolverInterface::class);
        $provider = new FileModelTemplateResolver(
            'App\\Views',
            vfsStream::url('templates'),
            '.blade.php',
            false,
            $modelNamespaceProviderMock,
            $modelNameProviderMock
        );

        // when
        $result = fn() => $provider->resolveModelTemplate($templateModel);

        // then
        $modelNamespaceProviderMock->shouldReceive('resolveModelNamespace')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views');
        $modelNameProviderMock->shouldReceive('resolveModelName')
            ->once()
            ->with($templateModel)
            ->andReturn('SampleView');

        $this->assertSame('', $result());

        // apply
        Mockery::close();
    }

    public function testGetFileBasedTemplateReturnsPathForMissingFile(): void
    {
        // given
        vfsStream::setup('templates');
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $modelNamespaceProviderMock = Mockery::mock(ModelNamespaceResolverInterface::class);
        $modelNameProviderMock = Mockery::mock(ModelNameResolverInterface::class);
        $provider = new FileModelTemplateResolver(
            'App\\Views',
            vfsStream::url('templates'),
            '.blade.php',
            true,
            $modelNamespaceProviderMock,
            $modelNameProviderMock
        );

        // when
        $result = fn() => $provider->resolveModelTemplate($templateModel);

        // then
        $modelNamespaceProviderMock->shouldReceive('resolveModelNamespace')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views');
        $modelNameProviderMock->shouldReceive('resolveModelName')
            ->once()
            ->with($templateModel)
            ->andReturn('SampleView');

        $this->assertSame(vfsStream::url('templates/sample-view.blade.php'), $result());

        // apply
        Mockery::close();
    }

    public function testGetTemplateHandlesCamelCaseConversion(): void
    {
        // given
        vfsStream::setup('templates', null, ['some-camel-case-view.blade.php' => 'Camel Case Content']);
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $modelNamespaceProviderMock = Mockery::mock(ModelNamespaceResolverInterface::class);
        $modelNameProviderMock = Mockery::mock(ModelNameResolverInterface::class);
        $provider = new FileModelTemplateResolver(
            'App\\Views',
            vfsStream::url('templates'),
            '.blade.php',
            false,
            $modelNamespaceProviderMock,
            $modelNameProviderMock
        );

        // when
        $result = fn() => $provider->resolveModelTemplate($templateModel);

        // then
        $modelNamespaceProviderMock->shouldReceive('resolveModelNamespace')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views');
        $modelNameProviderMock->shouldReceive('resolveModelName')
            ->once()
            ->with($templateModel)
            ->andReturn('SomeCamelCaseView');

        $this->assertSame('Camel Case Content', $result());

        // apply
        Mockery::close();
    }

    public function testGetFileBasedTemplateHandlesCamelCaseConversion(): void
    {
        // given
        vfsStream::setup('templates', null, ['some-camel-case-view.blade.php' => 'Camel Case Content']);
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $modelNamespaceProviderMock = Mockery::mock(ModelNamespaceResolverInterface::class);
        $modelNameProviderMock = Mockery::mock(ModelNameResolverInterface::class);
        $provider = new FileModelTemplateResolver(
            'App\\Views',
            vfsStream::url('templates'),
            '.blade.php',
            true,
            $modelNamespaceProviderMock,
            $modelNameProviderMock
        );

        // when
        $result = fn() => $provider->resolveModelTemplate($templateModel);

        // then
        $modelNamespaceProviderMock->shouldReceive('resolveModelNamespace')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views');
        $modelNameProviderMock->shouldReceive('resolveModelName')
            ->once()
            ->with($templateModel)
            ->andReturn('SomeCamelCaseView');

        $this->assertSame(vfsStream::url('templates/some-camel-case-view.blade.php'), $result());

        // apply
        Mockery::close();
    }

    public function testGetTemplateHandlesNestedNamespaces(): void
    {
        // given
        vfsStream::setup('templates', null, ['Admin/dashboard-view.blade.php' => 'Dashboard Content']);
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $modelNamespaceProviderMock = Mockery::mock(ModelNamespaceResolverInterface::class);
        $modelNameProviderMock = Mockery::mock(ModelNameResolverInterface::class);
        $provider = new FileModelTemplateResolver(
            'App\\Views',
            vfsStream::url('templates'),
            '.blade.php',
            false,
            $modelNamespaceProviderMock,
            $modelNameProviderMock
        );

        // when
        $result = fn() => $provider->resolveModelTemplate($templateModel);

        // then
        $modelNamespaceProviderMock->shouldReceive('resolveModelNamespace')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views\\Admin');
        $modelNameProviderMock->shouldReceive('resolveModelName')
            ->once()
            ->with($templateModel)
            ->andReturn('DashboardView');

        $this->assertSame('Dashboard Content', $result());

        // apply
        Mockery::close();
    }
    public function testGetFileBasedTemplateHandlesNestedNamespaces(): void
    {
        // given
        vfsStream::setup('templates', null, ['admin/dashboard-view.blade.php' => 'Dashboard Content']);
        $templateModel = Mockery::mock(TemplateModelInterface::class);
        $modelNamespaceProviderMock = Mockery::mock(ModelNamespaceResolverInterface::class);
        $modelNameProviderMock = Mockery::mock(ModelNameResolverInterface::class);
        $provider = new FileModelTemplateResolver(
            'App\\Views',
            vfsStream::url('templates'),
            '.blade.php',
            true,
            $modelNamespaceProviderMock,
            $modelNameProviderMock
        );

        // when
        $result = fn() => $provider->resolveModelTemplate($templateModel);

        // then
        $modelNamespaceProviderMock->shouldReceive('resolveModelNamespace')
            ->once()
            ->with($templateModel)
            ->andReturn('App\\Views\\Admin');
        $modelNameProviderMock->shouldReceive('resolveModelName')
            ->once()
            ->with($templateModel)
            ->andReturn('DashboardView');

        $this->assertSame(vfsStream::url('templates/Admin/dashboard-view.blade.php'), $result());

        // apply
        Mockery::close();
    }
}
