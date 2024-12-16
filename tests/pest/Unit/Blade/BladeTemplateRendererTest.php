<?php

declare(strict_types=1);

namespace Tests\Unit\Blade;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\Interfaces\CodeExecutorInterface;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Modules\RendererModulesInterface;
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Blade\BladeRendererModules;

class BladeTemplateRendererTest extends TestCase
{
    //// testMakes

    public function testMakesTemplateRenderer(): void
    {
        $this->testUsesGivenModule(null, function (RendererModulesInterface $modules) {
            $this->assertNotNull($modules->getTemplateRenderer());
        });
    }

    public function testMakesEventDispatcher(): void
    {
        $this->testUsesGivenModule(null, function (RendererModulesInterface $modules) {
            $this->assertNotNull($modules->getEventDispatcher());
        });
    }

    public function testMakesTemplateCompiler(): void
    {
        $this->testUsesGivenModule(null, function (RendererModulesInterface $modules) {
            $this->assertNotNull($modules->getTemplateCompiler());
        });
    }

    public function testMakesCodeExecutor(): void
    {
        $this->testUsesGivenModule(null, function (RendererModulesInterface $modules) {
            $this->assertNotNull($modules->getCodeExecutor());
        });
    }

    //// testUsesGiven

    public function testUsesGivenTemplateRenderer(): void
    {
        // given
        $module = Mockery::mock(TemplateRendererInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (RendererModulesInterface $modules) use ($module) {
                $modules->setTemplateRenderer($module);
            },
            function (RendererModulesInterface $modules) {
                $modules->getTemplateRenderer()->renderTemplate('', []);
            }
        );

        // then
        $module->shouldReceive('renderTemplate')
            ->once()
            ->andReturn('');

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenEventDispatcher(): void
    {
        // given
        $module = Mockery::mock(EventDispatcherInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (RendererModulesInterface $modules) use ($module) {
                $modules->setEventDispatcher($module);
            },
            function (RendererModulesInterface $modules) {
                $modules->getEventDispatcher()->attachEventDetails('test', []);
            }
        );

        // then
        $module->shouldReceive('attachEventDetails')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenTemplateCompiler(): void
    {
        // given
        $module = Mockery::mock(TemplateCompilerInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (RendererModulesInterface $modules) use ($module) {
                $modules->setTemplateCompiler($module);
            },
            function (RendererModulesInterface $modules) {
                $modules->getTemplateCompiler()->compileTemplate('test');
            }
        );

        // then
        $module->shouldReceive('compileTemplate')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenCodeExecutor(): void
    {
        // given
        $module = Mockery::mock(CodeExecutorInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (RendererModulesInterface $modules) use ($module) {
                $modules->setCodeExecutor($module);
            },
            function (RendererModulesInterface $modules) {
                $modules->getCodeExecutor()->executeCode('test');
            }
        );

        // then
        $module->shouldReceive('executeCode')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    /**
     * We use a wrapper, case given objects are often decorated (wrapped),
     * so we can't compare the response by the plain comparison.
     *
     * @param callable(RendererModulesInterface $modules):void $setModuleCallback
     * @param callable(RendererModulesInterface $modules):void $callModuleCallback
     */
    protected function testUsesGivenModule(?callable $setModuleCallback, callable $callModuleCallback): void
    {
        // given
        $modules = new BladeRendererModules();
        $config = new BladeRendererConfig();

        if (null !== $setModuleCallback) {
            $setModuleCallback($modules);
        }

        $config->setModules($modules);

        // when
        $makeRenderer = fn()=> new BladeTemplateRenderer($config);

        // then
        $callModuleCallback($makeRenderer()->getModules());
    }
}
