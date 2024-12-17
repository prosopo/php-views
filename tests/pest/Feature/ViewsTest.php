<?php

declare(strict_types=1);

namespace Tests\Feature;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\TemplateModel;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\Views;

class ViewsTest extends TestCase
{
    public function testRenderModelThatImplementsInterface(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
            [
                'defaultValue' => '"Hello World!"',
                'name' => 'message',
                'type' => 'string',
                'visibility' => 'private',
            ]
            ],
            false
        );
        $views = new Views();

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderModelThatImplementInterfaceIgnoresPublicProperties(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}{{ $data }}']);
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'defaultValue' => '"Hello World!"',
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'private',
                ],
                [
                    'defaultValue' => '"Internal"',
                    'name' => 'data',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            false,
            ['data',]
        );
        $views = new Views();

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = new $modelClass();

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderModelThatExtendsBaseClass(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $views = new Views();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            true
        );

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';
        $model = $views->makeModel($modelClass);
        $model->message = 'Hello World!';

        // then
        $this->assertSame('Hello World!', $views->renderModel($model));
    }

    public function testRenderCallsSetupCallback(): void
    {
        // given
        vfsStream::setup('templates', null, ['first-model.blade.php' => '{{ $message }}']);
        $bladeRenderer = new BladeTemplateRenderer();
        $namespaceConfig = (new ViewNamespaceConfig($bladeRenderer))
            ->setTemplatesRootPath(vfsStream::url('templates'))
            ->setTemplateFileExtension('.blade.php');
        $views = new Views();

        $modelNamespace = $this->defineRealModelClass(
            __METHOD__,
            'FirstModel',
            [
                [
                    'name' => 'message',
                    'type' => 'string',
                    'visibility' => 'public',
                ]
            ],
            true
        );

        // when
        $views->addNamespace($modelNamespace, $namespaceConfig);

        $modelClass = $modelNamespace . '\\FirstModel';

        // then
        $this->assertSame('Hello World!', $views->renderModel($modelClass, function ($model) {
            $model->message = 'Hello World!';
        }));
    }

    /**
     * @param array<int,array{visibility:string,name:string, type?:string, defaultValue?:mixed}> $properties
     * @param string[] $tempatePropertiesNamesToIgnore
     */
    protected function defineRealModelClass(
        string $method,
        string $className,
        array $properties,
        bool $extendsClass,
        array $tempatePropertiesNamesToIgnore = []
    ): string {
        $methodName = explode('::', $method);
        $methodName = $methodName[count($methodName) - 1];

        $namespace = '_views_test_' . strtolower($methodName);
        $classContent = $this->getClassProperties($properties);
        $extends = true === $extendsClass ?
            'extends \\' . TemplateModel::class :
            'implements \\' . TemplateModelInterface::class;

        if (false === $extendsClass) {
            $classContent .= $this->makeTemplateArgumentsMethod($properties, $tempatePropertiesNamesToIgnore);
        }

        $code = sprintf('namespace %s; class %s %s { %s }', $namespace, $className, $extends, $classContent);

        eval($code);

        return $namespace;
    }

    /**
     * @param array<int,array{visibility:string,name:string, type?:string, defaultValue?:mixed}> $properties
     */
    protected function getClassProperties(array $properties): string
    {
        return array_reduce($properties, function ($classContent, $property) {
            $defaultValue = $property['defaultValue'] ?? null;
            $defaultValue = null !== $defaultValue ?
                sprintf(' = %s', $defaultValue) :
                '';

            return $classContent . sprintf(
                '%s %s $%s %s;',
                $property['visibility'],
                $property['type'] ?? '',
                $property['name'],
                $defaultValue
            );
        },
            '');
    }

    /**
     * @param array<string,string|null> $properties name => type|null
     * @param string[] $templatePropertiesNamesToIgnore
     */
    protected function makeTemplateArgumentsMethod(array $properties, array $templatePropertiesNamesToIgnore): string
    {
        $properties = array_filter($properties, function ($property) use ($templatePropertiesNamesToIgnore) {
            return false === in_array($property['name'], $templatePropertiesNamesToIgnore);
        });

        $arrayContent = array_reduce($properties, function ($arrayContent, $property) {
            return $arrayContent . sprintf(
                '"%s" => $this->%1$s,',
                $property['name']
            );
        }, '');

        return sprintf('function getTemplateArguments():array{return [%s];}', $arrayContent);
    }

    // fixme makeModel tests

    // todo calls errorCallback

    // todo supports innerModels

    // todo supports multiple namespaces

    // todo allows to mix models from diff namespaces

    // todo can be used with custom compiler (for pure .php)
}
