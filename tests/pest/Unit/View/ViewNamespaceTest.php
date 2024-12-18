<?php

declare(strict_types=1);

namespace Tests\Unit\View;

use Mockery;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelNameResolverInterface;
use Prosopo\Views\Interfaces\Model\ModelNamespaceResolverInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\Interfaces\Object\ObjectPropertyWriterInterface;
use Prosopo\Views\Interfaces\Object\ObjectReaderInterface;
use Prosopo\Views\Interfaces\Object\PropertyValueProviderInterface;
use Prosopo\Views\Interfaces\Template\ModelTemplateResolverInterface;
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\PrivateClasses\View\ViewNamespace;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewNamespaceModules;
use ReflectionProperty;
use stdClass;

class ViewNamespaceTest extends TestCase
{
    //// testMakes

    public function testMakesObjectReader()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getTemplateRenderer());
            }
        );
    }

    public function testMakesPropertyValueProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getPropertyValueProvider());
            }
        );
    }

    public function testMakesObjectPropertyWriter()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getObjectPropertyWriter());
            }
        );
    }

    public function testMakesModelFactory()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelFactory());
            }
        );
    }

    public function testMakesModelTemplateProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelTemplateResolver());
            }
        );
    }

    public function testMakesModelRenderer()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelRenderer());
            }
        );
    }

    public function testMakesEventDispatcher()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getEventDispatcher());
            }
        );
    }

    public function testMakesModelNameProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelNameResolver());
            }
        );
    }

    public function testMakesModelNamespaceProvider()
    {
        $this->testUsesGivenModule(
            null,
            function (ViewNamespaceModules $modules) {
                $this->assertNotNull($modules->getModelNamespaceResolver());
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
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setTemplateRenderer($module);
            },
            function (ViewNamespaceModules $modules) {
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
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setObjectReader($module);
            },
            function (ViewNamespaceModules $modules) {
                $instance = Mockery::mock(StdClass::class);

                 $modules->getObjectReader()->extractObjectVariables($instance);
            }
        );

        // then
        $module->shouldReceive('extractObjectVariables')
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
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setPropertyValueProvider($module);
            },
            function (ViewNamespaceModules $modules) {
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
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setObjectPropertyWriter($module);
            },
            function (ViewNamespaceModules $modules) {
                $instance = Mockery::mock(StdClass::class);
                $propertyValueProvider = Mockery::mock(PropertyValueProviderInterface::class);

                $modules->getObjectPropertyWriter()->assignPropertyValues($instance, $propertyValueProvider);
            }
        );

        // then
        $module->shouldReceive('assignPropertyValues')
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
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelFactory($module);
            },
            function (ViewNamespaceModules $modules) {
                $modules->getModelFactory()->createModel('test');
            }
        );

        // then
        $module->shouldReceive('createModel')
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
        $module = Mockery::mock(ModelTemplateResolverInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelTemplateResolver($module);
            },
            function (ViewNamespaceModules $modules) {
                $model = Mockery::mock(TemplateModelInterface::class);

                $modules->getModelTemplateResolver()->resolveModelTemplate($model);
            }
        );

        // then
        $module->shouldReceive('resolveModelTemplate')
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
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelRenderer($module);
            },
            function (ViewNamespaceModules $modules) {
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
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setEventDispatcher($module);
            },
            function (ViewNamespaceModules $modules) {
                $modules->getEventDispatcher()->registerEventDetails('test', []);
            }
        );

        // then
        $module->shouldReceive('registerEventDetails')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelNameProvider(): void
    {
        // given
        $module = Mockery::mock(ModelNameResolverInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelNameResolver($module);
            },
            function (ViewNamespaceModules $modules) {
                $model = Mockery::mock(TemplateModelInterface::class);

                $modules->getModelNameResolver()->resolveModelName($model);
            }
        );

        // then
        $module->shouldReceive('resolveModelName')
            ->once();

        // apply
        $test();
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function testUsesGivenModelNamespaceProvider(): void
    {
        // given
        $module = Mockery::mock(ModelNamespaceResolverInterface::class);

        // when
        $test = fn()=>$this->testUsesGivenModule(
            function (ViewNamespaceModules $modules) use ($module) {
                $modules->setModelNamespaceResolver($module);
            },
            function (ViewNamespaceModules $modules) {
                $modules->getModelNamespaceResolver()->resolveModelNamespace('test');
            }
        );

        // then
        $module->shouldReceive('resolveModelNamespace')
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
     * @param callable(ViewNamespaceModules $modules):void $setModuleCallback
     * @param callable(ViewNamespaceModules $modules):void $callModuleCallback
     */
    protected function testUsesGivenModule(?callable $setModuleCallback, callable $callModuleCallback): void
    {
        // given
        $templateRendererMock = Mockery::mock(TemplateRendererInterface::class);
        $modelFactoryMock = Mockery::mock(ModelFactoryInterface::class);
        $modelRendererMock = Mockery::mock(ModelRendererInterface::class);

        $modules = new ViewNamespaceModules($templateRendererMock);
        $config = new ViewNamespaceConfig($templateRendererMock);

        if (null !== $setModuleCallback) {
            $setModuleCallback($modules);
        }

        $config->setModules($modules);

        // when
        $makeNamespace = fn()=> new ViewNamespace('', $config, $modelFactoryMock, $modelRendererMock);

        // then
        $callModuleCallback($makeNamespace()->getModules());
    }
}
