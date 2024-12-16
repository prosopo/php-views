<?php

declare(strict_types=1);

namespace Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Modules\ModulesInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateProviderInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\Modules;
use Prosopo\Views\PrivateClasses\ViewsNamespace;
use Prosopo\Views\ViewsNamespaceConfig;
use ReflectionProperty;
use stdClass;

class ViewsNamespaceTest extends TestCase
{
    //// testMakes

    public function testMakesObjectReader()
    {
        $this->testUsesGivenModule(
            null,
            function (ModulesInterface $modules) {
                $this->assertNotNull($modules->getTemplateRenderer());
            }
        );
    }

    public function testMakesPropertyValueProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ModulesInterface $modules) {
                $this->assertNotNull($modules->getPropertyValueProvider());
            }
        );
    }

    public function testMakesObjectPropertyWriter()
    {
        $this->testUsesGivenModule(
            null,
            function (ModulesInterface $modules) {
                $this->assertNotNull($modules->getObjectPropertyWriter());
            }
        );
    }

    public function testMakesModelFactory()
    {
        $this->testUsesGivenModule(
            null,
            function (ModulesInterface $modules) {
                $this->assertNotNull($modules->getModelFactory());
            }
        );
    }

    public function testMakesModelTemplateProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ModulesInterface $modules) {
                $this->assertNotNull($modules->getModelTemplateProvider());
            }
        );
    }

    public function testMakesModelRenderer()
    {
        $this->testUsesGivenModule(
            null,
            function (ModulesInterface $modules) {
                $this->assertNotNull($modules->getModelRenderer());
            }
        );
    }

    public function testMakesEventDispatcher()
    {
        $this->testUsesGivenModule(
            null,
            function (ModulesInterface $modules) {
                $this->assertNotNull($modules->getEventDispatcher());
            }
        );
    }

    //// testUsesGiven

    public function testUsesGivenTemplateRendererReader(): void
    {
        // given
        $module = Mockery::mock(TemplateRendererInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ModulesInterface $modules) use ($module) {
                $modules->setTemplateRenderer($module);
            },
            function (ModulesInterface $modules) {
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

    public function testUsesGivenObjectReader(): void
    {
        // given
        $module = Mockery::mock(ObjectReaderInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ModulesInterface $modules) use ($module) {
                $modules->setObjectReader($module);
            },
            function (ModulesInterface $modules) {
                $instance = Mockery::mock(StdClass::class);

                 $modules->getObjectReader()->getObjectVariables($instance);
            }
        );

        // then
        $module->shouldReceive('getObjectVariables')
            ->once()
            ->andReturn([]);

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenPropertyValueProvider(): void
    {
        // given
        $module = Mockery::mock(PropertyValueProviderInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ModulesInterface $modules) use ($module) {
                $modules->setPropertyValueProvider($module);
            },
            function (ModulesInterface $modules) {
                $reflectionProperty = Mockery::mock(ReflectionProperty::class);

                $modules->getPropertyValueProvider()->getPropertyValue($reflectionProperty);
            }
        );

        // then
        $module->shouldReceive('supportsProperty')
            ->andReturn(true);
        $module->shouldReceive('getPropertyValue')
            ->once()
            ->andReturn('');

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenObjectPropertyWriter(): void
    {
        // given
        $module = Mockery::mock(ObjectPropertyWriterInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ModulesInterface $modules) use ($module) {
                $modules->setObjectPropertyWriter($module);
            },
            function (ModulesInterface $modules) {
                $instance = Mockery::mock(StdClass::class);

                $modules->getObjectPropertyWriter()->setObjectPropertyValues($instance);
            }
        );

        // then
        $module->shouldReceive('setObjectPropertyValues')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelFactory(): void
    {
        // given
        $module = Mockery::mock(ModelFactoryInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ModulesInterface $modules) use ($module) {
                $modules->setModelFactory($module);
            },
            function (ModulesInterface $modules) {
                $modules->getModelFactory()->makeModel('test');
            }
        );

        // then
        $module->shouldReceive('makeModel')
            ->once()
             ->andReturn(null);

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelTemplateProvider(): void
    {
        // given
        $module = Mockery::mock(ModelTemplateProviderInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ModulesInterface $modules) use ($module) {
                $modules->setModelTemplateProvider($module);
            },
            function (ModulesInterface $modules) {
                $model = Mockery::mock(TemplateModelInterface::class);

                $modules->getModelTemplateProvider()->getModelTemplate($model);
            }
        );

        // then
        $module->shouldReceive('getModelTemplate')
            ->once()
            ->andReturn('');

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelRenderer(): void
    {
        // given
        $module = Mockery::mock(ModelRendererInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ModulesInterface $modules) use ($module) {
                $modules->setModelRenderer($module);
            },
            function (ModulesInterface $modules) {
                $model = Mockery::mock(TemplateModelInterface::class);

                $modules->getModelRenderer()->renderModel($model);
            }
        );

        // then
        $module->shouldReceive('renderModel')
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
            function (ModulesInterface $modules) use ($module) {
                $modules->setEventDispatcher($module);
            },
            function (ModulesInterface $modules) {
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

    /**
     * We use a wrapper, case given objects are often decorated (wrapped),
     * so we can't compare the response by the plain comparison.
     *
     * @param callable(ModulesInterface $modules):void $setModuleCallback
     * @param callable(ModulesInterface $modules):void $callModuleCallback
     */
    protected function testUsesGivenModule(?callable $setModuleCallback, callable $callModuleCallback): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $modelRendererMock = Mockery::mock(ModelRendererInterface::class);

        $modules = new Modules($templateRendererMock);
        $config = new ViewsNamespaceConfig($templateRendererMock);

        if (null !== $setModuleCallback) {
            $setModuleCallback($modules);
        }

        $config->setModules($modules);

        // when
        $makeNamespace = fn()=> new ViewsNamespace($config, $modelFactoryMock, $modelRendererMock);

        // then
        $callModuleCallback($makeNamespace()->getModules());
    }
}
