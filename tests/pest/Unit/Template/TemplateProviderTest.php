<?php

declare(strict_types=1);

namespace Tests\Unit\Template;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\PrivateClasses\Template\TemplateProvider;

class TemplateProviderTest extends TestCase
{
    public function testGetTemplateReturnsFileContentForValidView(): void
    {
        // given
         vfsStream::setup('templates', null, ['sample-view.blade.php' => 'View Content']);
        $provider = new TemplateProvider(vfsStream::url('templates'), 'App\\Views', '.blade.php');

        $viewClass = 'App\\Views\\SampleView';

        // when
        $result = fn() => $provider->getTemplate($viewClass);

        // then
        $this->assertSame('View Content', $result());
    }

    public function testGetTemplateReturnsEmptyStringForMissingFile(): void
    {
        // given
        vfsStream::setup('templates'); // Empty directory
        $provider = new TemplateProvider(vfsStream::url('templates'), 'App\\Views', '.blade.php');

        $viewClass = 'App\\Views\\MissingView';

        // when
        $result = fn() => $provider->getTemplate($viewClass);

        // then
        $this->assertSame('', $result());
    }

    public function testGetTemplateHandlesCamelCaseConversion(): void
    {
        // given
        vfsStream::setup('templates', null, ['some-camel-case-view.blade.php' => 'Camel Case Content']);
        $provider = new TemplateProvider(vfsStream::url('templates'), 'App\\Views', '.blade.php');

        $viewClass = 'App\\Views\\SomeCamelCaseView';

        // when
        $result = fn() => $provider->getTemplate($viewClass);

        // then
        $this->assertSame('Camel Case Content', $result());
    }

    public function testGetTemplateHandlesNestedNamespaces(): void
    {
        // given
        vfsStream::setup('templates', null, [
            'admin/dashboard-view.blade.php' => 'Dashboard Content'
        ]);
        $provider = new TemplateProvider(vfsStream::url('templates'), 'App\\Views', '.blade.php');

        $viewClass = 'App\\Views\\Admin\\DashboardView';

        // when
        $result = fn() => $provider->getTemplate($viewClass);

        // then
        $this->assertSame('Dashboard Content', $result());
    }
}
