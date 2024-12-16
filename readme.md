# PHP Views

Blazing fast PHP Views with model-driven approach, multi-namespace support and
built-in [Blade](https://laravel.com/docs/11.x/blade) implementation as a default template engine.

### Benefits

* Zero Dependencies: Lightweight and easy to integrate into any project.
* Wide Compatibility: PHP 7.4+, 8.0+
* Adherence to the [SOLID principles](https://en.wikipedia.org/wiki/SOLID): You can override any module without
  affecting the rest of the system.
* Namespace Support: Manage different templates seamlessly under a unified structure.
* Test Coverage: Covered by [Pest](https://pestphp.com/) Unit and Feature tests.
* Static Analysis: Checked by [PHPStan](https://phpstan.org/).

### Flexible Usage

You're free to use the package in your own way:

* Use it as your Views provider, combining model-driven approach with the built-in Blade.
* Employ its standalone [Blade](https://laravel.com/docs/11.x/blade) implementation as a template engine for your
  Blade templates.
* Leverage its model-driven approach for any template engine (e.g., [Twig](https://twig.symfony.com/)).
* Use it as a connector for templates that utilize different template engines.

## Table of Contents

- [1. Model-driven approach](#1-model-driven-approach)
- [2. Views](#2-views)
- [3. Built-in standalone Blade implementation](#3-built-in-standalone-blade-implementation)
- [4. Contribution](#4-contribution)
- [5. Credits](#5-credits)

## 1. Model-driven approach

Similar to many frameworks, such as Laravel, this package embraces a model-driven approach to templates. Each template
is paired with its own Model, where the Model's public properties and methods act as arguments available within the
template.

Model class:

```php
namespace MyPackage\Views;

use Prosopo\Views\TemplateModel;
use Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class EmployeeTemplateModel extends TemplateModel
{
    public int $salary;
    public int $bonus;
    // Use the abstract interface to accept any Model. 
    public TemplateModelInterface $innerModel;
    // Use a specific class only when you want to restrict the usage to it.
    public CompanyTemplateModel $company;

    public function total(): int
    {
        return $this->salary + $this->bonus;
    }
}
```

Model template (Blade is used in this example):

```php
<p>
Your month income is {{ $total() }}, 
from which {{ $salary }} is a salary, and {{ $bonus }} is a bonus.
</p>

{!! $innerModel !!}

<p>Company info:</p>

{!! $company !!}
```

### 1.2) Benefits of the model-driven approach

1. Typed variables: Eliminate the hassle of type matching and renaming associated with array-driven variables.
2. Reduced Routine: During object creation, public fields of the model without default values are automatically
   initialized with default values.
3. Enhanced Access: Public methods are made available to the template alongside the variables.
4. Unified Interface:  Use the `ViewInterface` in your application when accepting or returning a `View` to maintain
   flexibility and avoid specifying the exact component.

The `View` class implements the `View_Interface`. During rendering, any inner objects that also implement
`View_Interface` will be automatically rendered and passed into the template as strings.

### 1.3) Custom property defaults

Note: In the `View` class, in order to satisfy the Model creator, the constructor is marked as final. If you need to
set custom default values, consider using one of the following approaches:

```php
namespace MyPackage\Views;

use Prosopo\Views\TemplateModel;

class EmployeeTemplateModel extends TemplateModel
{
    // approach for plain field types.
    public int $varWithCustomDefaultValue = 'custom default value';
    public Company $company;

    protected function setCustomDefaults(){
        // approach for object field types.
        $this->company = new Company();
    }
}
```

> Tip: If your app Models require additional object dependencies, you can override the `PropertyValueProvider`
> module to
> integrate with a Dependency Injection container like [PHP-DI](https://php-di.org/). This allows model properties to be
> automatically resolved
> while object creation by your application's DI system.

### 1.4) Custom Model implementation (advanced usage)

The only requirement for a Model is to implement the `TemplateModelInterface`. This means you can transform any class
into a Model without needing to extend a specific base class, or define public properties:

```php
namespace MyPackage\Views;

use Prosopo\Views\Interfaces\Model\TemplateModelInterface;

class AnyClass implements TemplateModelInterface {  
     public function getTemplateArguments(): array {
        // you can fill out arguments from any source or define manually.
        return [
           'name' => 'value',
        ];
     }
}
```

## 2. Views

`Views` is the root class of this package, responsible for initializing all modules, including the `TemplateProvider`
module,
which automates the linking of Models to their respective templates.

### 2.1) Flat setup

```php
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\ViewsNamespaceConfig;
use Prosopo\Views\Views;

// 1. Make the Template Renderer.
// It can be the built-in Blade or any external one
// (we'll show the external one in a separate chapter)

$bladeRendererConfig = new BladeRendererConfig();
$bladeRenderer = new BladeTemplateRenderer($bladeRendererConfig);

// 2. Define the namespace config

$namespaceConfig = (new ViewsNamespaceConfig($bladeRenderer))
    // required settings:
    ->setTemplatesRootPath(__DIR__ . './templates')
    ->setModelsRootNamespace('MyPackage\Views')
    ->setTemplateFileExtension('.blade.php')
    // optional settings:
    ->setTemplateErrorHandler(function (array $eventDetails) {
        // logging, notifying, whatever.
    });

// This line is necessary only if you defined the templateErrorHandler
$namespaceConfig->getModules()
    ->setEventDispatcher($bladeRenderer->getModules()->getEventDispatcher());

// 3. Make the Views:

$views = new Views();

// 4. Add the namespace (you can have multiple namespaces)

$views->addNamespace($namespaceConfig);
```

### 2.2) Single-step Model creation and rendering

You can create, set values, and render a Model in a single step using the callback argument of the `renderView` method,
as
shown below:

```php
echo $views->renderModel(
    EmployeeModel::class,
    function (EmployeeModel $employee) use ($salary, $bonus) {
        $employee->salary = $salary;
        $employee->bonus = $bonus;
    }
);

// Tip: pass true to the third renderModel() argument to print it without echo.
```

This approach enables a functional programming style when working with Models.

### 2.3) Multi-step creation and rendering

When you need split creation, use the factory to create the model, and then render later when you need it.

```php
$employee = $views->makeModel(EmployeeModel::class);

// ...

$employee->salary = $salary;
$employee->bonus = $bonus;

// ...

echo $views->renderModel($employee);

// Tip: you can still pass the callback as the second renderModel() argument
// to customize the Model properties before rendering. 
```

### 2.4) Automated templates matching

The built-in `TemplateProvider` automatically matches templates based on the Model names and their relative namespaces.
This automates the process of associating templates with their corresponding Models.

Example:

- src/
    - Views/
        - MyView.php
        - settings
            - GeneralSettings.php
    - templates/
        - my-view{.blade.php}
        - settings/
            - general-settings{.blade.php}

**Naming Note:** Use dashes in template names, as camelCase in Model names is automatically converted to dash-separated
names.

### 2.5) Custom modules

By default, the `Views` class creates module instances using classes from the current package.

If you need to override the default module behavior, you can define a custom implementation in the
configuration. The `Views` class will use the specified implementation.

> Tip: You can see the full list of the modules in the `ModulesInterface`.

#### Example: Using Twig as a Template Renderer (instead of the built-in Blade Renderer)

```php

// 1. Make a facade (for Twig or another template engine)

use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\ViewsNamespaceConfig;

class TwigDecorator implements TemplateRendererInterface {
    private $twig;

    public function __construct() {
        // todo init Twig or another engine.
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string {
        return $this->twig->render($template, $variables, $doPrint);
    }
}

// 2. Define the namespace config with the facade instance

$twigDecorator = new TwigDecorator();

$namespaceConfig = (new ViewsNamespaceConfig($twigDecorator))
    ->setTemplatesRootPath(__DIR__ . './templates')
    ->setViewsRootNamespace('MyPackage\Views')
    ->setTemplateFileExtension('.twig')

// 3. Make the Views:

$views = new Views();

// 4. Add the namespace (you can have multiple namespaces)

$views->addNamespace($namespaceConfig);
```

> Note: The package includes only the Blade implementation. If you wish to use a different template engine, you will
> need to install its Composer package and create a facade object, as demonstrated above.

### 2.6) Namespace mixing

> Fun Fact: The `Views` class not only supporting multiple namespaces, but also enabling you to use Models from one
> namespace within another, regardless of their setup.

Example of multi-namespace usage:

Suppose you have a namespace for Blade templates, including a `Button` model and a `button.blade.php` template.

Additionally, you have a namespace for Twig templates, with a `Popup` model and a `popup.twig` template.

Here’s the cool part: you can safely use `Button` as a property of the `Popup` model. The package will first render the
`Button` using Twig, converting it to a string, and then pass it seamlessly into the Blade template of the `Popup`.

## 3. Built-in standalone Blade implementation

[Blade](https://laravel.com/docs/11.x/blade) is an elegant and powerful template engine originally designed
for [Laravel](https://laravel.com/).

However, since it isn't available as a standalone package, this package includes its own Blade compiler.

It provides full support for [Blade's key features](https://laravel.com/docs/11.x/blade)
while remaining completely independent of Laravel.

### 3.1) Supported features

The following Blade tokens are supported:

1. Displaying: `{{ $var }}` and `{!! $var }}`
2. IF Conditions: `@if`, `@else`, `@elseif`, `@endif`
3. Switch conditions: `@switch`, `@case`, `@break`, `@default`, `@endswitch`.
4. Loops: `@foreach`, `@endforeach`, `@for`, `@endfor`, `@break`.
5. Helpers: `@selected`, `@checked`, `@class`.
6. PHP-related: `@use`, `@php`, `@endphp`.

Visit the [official Blade docs](https://laravel.com/docs/11.x/blade) to learn about their usage.

### 3.2) Flat setup

```php
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\Blade\BladeRendererConfig;

$bladeRendererConfig = new BladeRendererConfig();
$bladeRenderer = new BladeTemplateRenderer($bladeRendererConfig);

echo $bladeRenderer->renderTemplate('/my-template.blade.php', [
    'var' => true
]);
```

> Tip #1: by default, `BladeTemplateRenderer` is configured to work with files, but you can also switch it to work with
> plain strings:

```php
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\Blade\BladeRendererConfig;

$bladeRendererConfig = new BladeRendererConfig();
$bladeRendererConfig->setIsFileBasedTemplate(false);

$bladeRenderer = new BladeTemplateRenderer($bladeRendererConfig);

echo $bladeRenderer->renderTemplate('@if($var)The variable is set.@endif', [
    'var' => true
]);
```

> Tip #2: As you see, the built-in Blade implementation is fully standalone and independent of the `Views` class. This
> means that even if you can't to use the model-driven approach, you can still utilize it as an independent Blade
> compiler.

### 3.3) Available Blade Renderer settings

The built-in Blade implementation includes a variety of settings that let you customize features such as escaping,
error handling, and more:

```php
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;

$bladeRendererConfig = (new BladeRendererConfig())
// By default, the Renderer expect a file name.
// Set to false if to work with strings
->setIsFileBasedTemplate(true)
->setTemplateErrorHandler(function (array $eventDetails): void {
    // Can be used for logging, notifying, etc.
})
->setCustomOutputEscapeCallback(function ($variable): string {
    if (
        false === is_string($variable) &&
        false === is_numeric($variable)
    ) {
        return '';
    }

    // htmlspecialchars is the default one.
    return htmlentities((string)$variable, ENT_QUOTES, 'UTF-8', false);
})
->setGlobalVariables([
    'sum' => function (int $a, int $b): string {
        return (string)($a + $b);
    },
    'variable' => 'value',
    ])
->setEscapeVariableName('escape')
->setCompilerExtensionCallback(function (string $template): string {
    // note: just an example, @use is supported by default.
    return (string)preg_replace('/@use\s*\((["\'])(.*?)\1\)/s', '<?php use $2; ?>', $template);
});
```

### 3.4) Notes on the standalone Blade implementation

You may have come across packages that attempt to adapt the official Blade engine by creating
stubs for its Laravel dependencies, such as the [jenssegers/blade](https://github.com/jenssegers/blade) package.
However, we chose not to adopt this approach for several reasons:

* PHP Version Requirements: It mandates PHP 8.2 or higher.
* External Dependencies: It introduces additional external dependencies.
* Potential Breakage: It can become unstable with future Laravel updates (as demonstrated
  by [past incidents](https://github.com/jenssegers/blade/issues/74).
* Limited Flexibility: Since it wasn’t designed as a standalone component, it lacks some of the customization abilities.

Thanks to great Blade's conceptual design, our compiler implementation required fewer than 200 lines of code.

### 3.5) Custom Blade Renderer modules

By default, the `BladeTemplateRenderer` creates module instances using classes from the current package.

If you need to override the default module behavior, you can define a custom implementation in the
configuration. The `BladeTemplateRenderer` will use the specified implementation.

> Tip: You can see the full list of the modules in the `RendererModulesInterface`.

#### Example: Defining a custom Blade compiler

```php
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;

class MyOwnBladeCompiler implements TemplateCompilerInterface {
    public function compileTemplate(string $template): string {
       // todo your custom compiler.
    }
}

// ...

$bladeRendererConfig->getModules()
    ->setTemplateCompiler(new MyOwnBladeCompiler());
```

> Note: Carefully review the `BladeRendererConfig` settings before creating a custom implementation. For example, in the
> case of the `BladeComplier`, it includes the `compilerExtensionCallback` setting, which allows you to add a
> custom directive without the need to make the custom compiler from scratch.

## 4. Contribution

We would be excited if you decide to contribute! Please read the `for-devs.md` file for project guidelines and
agreements.

## 5. Credits

This package was created by [Maxim Akimov](https://github.com/light-source/) during the development of
the [WordPress integration for Prosopo Procaptcha](https://wordpress.org/plugins/prosopo-procaptcha/).

[Procaptcha](https://prosopo.io/) is a privacy-friendly and cost-effective alternative to Google reCaptcha.

Consider using the Procaptcha service to protect your privacy and support the Prosopo team.
