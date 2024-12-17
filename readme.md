# PHP Views

Blazing fast PHP Views with model-driven approach, multi-namespace support and
custom [Blade](https://laravel.com/docs/11.x/blade) implementation as a default template engine.

### Benefits

* **Blazing fast:** Outperforms than the origin Laravel's Blade (see the [Benchmark chapter](#4-benchmark)).
* **Zero Dependencies:** Lightweight and easy to integrate into any project.
* **Wide Compatibility:** PHP 7.4+, 8.0+
* **Adherence to the [SOLID principles](https://en.wikipedia.org/wiki/SOLID):** The architecture allows you to easily
  override any module to meet specific requirements.
* **Namespace Support**: Manage different templates seamlessly under a unified structure.
* **Reliable**: Covered by [Pest](https://pestphp.com/) tests and checked by [PHPStan](https://phpstan.org/).

### Flexible Usage

You're free to use the package in your own way:

* Use it as your Views provider, combining model-driven approach with the built-in Blade.
* Employ its standalone [Blade](https://laravel.com/docs/11.x/blade) implementation as a template engine for your
  Blade templates.
* Leverage its model-driven approach for any templates (e.g., [Twig](https://twig.symfony.com/), or pure PHP).
* Use it as a connector for templates that utilize different template engines.

## Table of Contents

- [1. Model-driven approach](#1-model-driven-approach)
- [2. Views](#2-views)
- [3. View Renderer](#3-view-renderer)
- [4. Benchmark](#4-benchmark)
- [5. Contribution](#4-contribution)
- [6. Credits](#5-credits)

## 1. Model-driven approach

Similar to many frameworks, such as Laravel, this package embraces a model-driven approach to templates. Each template
is paired with its own Model, where the Model's public properties and methods act as arguments available within the
template.

Model class:

```php
namespace MyPackage\Views;

use Prosopo\Views\Interfaces\Model\TemplateModelInterface;
use Prosopo\Views\TemplateModel;

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
4. Unified Interface:  Use the `TemplateModelInterface` in your application when accepting or returning a Model to
   maintain
   flexibility and avoid specifying the exact component.

The `TemplateModel` class implements the `TemplateModelInterface`. During rendering, any inner objects that also
implement
`TemplateModelInterface` will be automatically rendered and passed into the template as strings.

### 1.3) Custom property defaults

Note: In the `TemplateModel` class, in order to satisfy the Model factory, the constructor is marked as final. If you
need to
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
into a Model without needing to extend a specific base class, or even define public properties:

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

The `Views` class provides the `addNamespace`, `makeModel` and `renderModel` methods. It acts as a
namespace manager and brings together different namespace configurations.

Each `ViewNamespace` has its own independent setup and set of modules. E.g. among these modules is the
`ModelTemplateProvider`, which automates the process of linking models to their
corresponding templates.

### 2.1) Setup

```php
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\Views;

// 1. Make the Template Renderer.
// (By default it uses the built-in Blade, but you can connect any)

$viewTemplateRenderer = new ViewTemplateRenderer();

// 2. Make the namespace config

$namespaceConfig = (new ViewNamespaceConfig($viewTemplateRenderer))
    // required settings:
    ->setTemplatesRootPath(__DIR__ . './templates')
    ->setTemplateFileExtension('.blade.php')
    // optional setting:
    ->setTemplateErrorHandler(function (array $eventDetails) {
        // logging, notifying, whatever.
    });

// (This line is necessary only if you defined the templateErrorHandler)
$namespaceConfig->getModules()
    ->setEventDispatcher($viewTemplateRenderer->getModules()->getEventDispatcher());

// 3. Make the Views instance:

$views = new Views();

// 4. Add the root namespace of your Template Models

$views->addNamespace('MyPackage\Views', $namespaceConfig);

// Tip: you can have multiple namespaces, and mix their Models.
```

### 2.2) Single-step Model creation and rendering

You can create, set values, and render a Model in a single step using the callback argument of the `renderView` method,
as shown below:

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

Advice: The `Views` class implements three interfaces: `ViewNamespaceManagerInterface` (for `addNamespace`),
`ModelFactoryInterface` (for
`makeModel`), and `ModelRendererInterface` (for `renderModel`).

When passing the `Views` instance to your methods, use
one of these interfaces as the argument type instead of the `Views` class itself.

This approach ensures that only the specific actions
you expect are accessible, promoting cleaner and more maintainable code.

### 2.4) Automated templates matching

The built-in `ModelTemplateProvider` automatically matches templates based on the Model names and their relative
namespaces. This automates the process of associating templates with their corresponding Models.

Example:

- src/
    - Views/
        - Homepage.php
        - settings
            - GeneralSettings.php
    - templates/
        - homepage{.blade.php}
        - settings/
            - general-settings{.blade.php}

**Naming Note:** Use dashes in template names, as camelCase in Model names is automatically converted to dash-separated
names.

> Tip: In case this approach doesn't work for your setup, you can override the `ModelTemplateProvider` module to
> implement your own logic. In case the reason is the name-specific only, consider overriding the `ModelNameProvider`
> module instead.

### 2.5) Custom modules

By default, the `addNamespace` class creates module instances for the namespace using classes from the current package.

If you need to override the default module behavior, you can define a custom implementation in the
configuration and the package will use the specified implementation.

> Tip: You can see the full list of the modules in the `ViewNamespaceModules` class.

#### Example: Using Twig as a Template Renderer (instead of the built-in Blade)

```php
// 1. Make a facade (for Twig or another template engine)

use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\Views;

class TwigDecorator implements TemplateRendererInterface
{
    private $twig;

    public function __construct()
    {
        // todo init Twig or another engine.
    }

    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string
    {
        return $this->twig->render($template, $variables, $doPrint);
    }
}

// 2. Define the namespace config with the facade instance

$twigDecorator = new TwigDecorator();

$namespaceConfig = (new ViewNamespaceConfig($twigDecorator))
    ->setTemplatesRootPath(__DIR__ . './templates')
    ->setTemplateFileExtension('.twig');

// 3. Make the Views:

$views = new Views();

// 4. Add the namespace (you can have multiple namespaces)

$views->addNamespace('MyPackage\Views', $namespaceConfig);
```

You can override any namespace module in the following way:

```php
$namespaceConfig->getModules()
     // override any module, like Factory:
    ->setModelFactory(new MyFactory());
```

> Note: The package includes only the Blade implementation. If you wish to use a different template engine,
> like Twig, you need to install its Composer package and create a facade object, as demonstrated above.

### 2.6) Namespace mixing

> Fun Fact: The `Views` class not only supporting multiple namespaces, but also enabling you to use Models from one
> namespace within another, preserving their individual setup.

Example of multi-namespace usage:

Suppose you have a namespace for Blade templates, including a `Button` model and a `button.blade.php` template.

Additionally, you have a namespace for Twig templates, with a `Popup` model and a `popup.twig` template.

Here’s the cool part: you can safely use `Button` as a property of the `Popup` model. The package will first render the
`Button` using Twig, converting it to a string, and then pass it seamlessly into the Blade template of the `Popup`.

## 3. View Renderer

`ViewTemplateRenderer` is the class responsible for rendering templates in this package. By default, it integrates the
Blade compiler, but it is fully customizable. You can replace the Blade compiler with your own implementation or use a
simple stub to enable support for plain PHP template files.

### 3.1) Built-in Blade integration

[Blade](https://laravel.com/docs/11.x/blade) is an elegant and powerful template engine originally designed
for [Laravel](https://laravel.com/).

However, since it isn't available as a standalone package, this package includes its own Blade compiler.

It provides full support for [Blade's key features](https://laravel.com/docs/11.x/blade)
while remaining completely independent of Laravel.

The following Blade tokens are supported:

1. Displaying: `{{ $var }}` and `{!! $var }}`
2. IF Conditions: `@if`, `@else`, `@elseif`, `@endif`
3. Switch conditions: `@switch`, `@case`, `@break`, `@default`, `@endswitch`.
4. Loops: `@foreach`, `@endforeach`, `@for`, `@endfor`, `@break`.
5. Helpers: `@selected`, `@checked`, `@class`.
6. PHP-related: `@use`, `@php`, `@endphp`.

Visit the [official Blade docs](https://laravel.com/docs/11.x/blade) to learn about their usage.

#### Notes on the standalone Blade implementation

You may have come across packages that attempt to adapt the official Blade engine by creating
stubs for its Laravel dependencies, such as the [jenssegers/blade](https://github.com/jenssegers/blade) package.
However, we chose not to adopt this approach for several reasons:

* PHP Version Requirements: It mandates PHP 8.2 or higher.
* External Dependencies: It introduces additional external dependencies.
* Potential Breakage: It can become unstable with future Laravel updates (as demonstrated
  by [past incidents](https://github.com/jenssegers/blade/issues/74).
* Limited Flexibility: Since it wasn’t designed as a standalone component, it lacks some of the customization abilities.
* Global functions: Laravel's implementation includes global helper functions, which becomes a problem when you need to
  [scope the package](https://github.com/humbug/php-scoper).

Thanks to great Blade's conceptual design, our compiler implementation required fewer than 200 lines of code.

### 3.2) View Renderer setup

```php
use Prosopo\Views\View\ViewTemplateRenderer;

$viewTemplateRenderer = new ViewTemplateRenderer();

echo $viewTemplateRenderer->renderTemplate('/my-template.blade.php', [
    'var' => true
]);
```

> Tip #1: by default, `BladeTemplateRenderer` is configured to work with files, but you can also switch it to work with
> plain strings:

```php
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\View\ViewTemplateRendererConfig;

$viewRendererConfig = new ViewTemplateRendererConfig();
$viewRendererConfig->setIsFileBasedTemplate(false);

$viewTemplateRenderer = new ViewTemplateRenderer($viewRendererConfig);

echo $viewTemplateRenderer->renderTemplate('@if($var)The variable is set.@endif', [
    'var' => true
]);
```

> Tip #2: As you see, the built-in TemplateRenderer implementation is fully standalone and independent of the `Views`
> class. This
> means that even if you can't or don't want to use the model-driven approach, you can still utilize it as an
> independent Blade compiler.

### 3.3) Available View Renderer settings

The `ViewTemplateRenderer` supports a variety of settings that let you customize features such as
escaping,
error handling, and more:

```php
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\View\ViewTemplateRendererConfig;

$viewRendererConfig = (new ViewTemplateRendererConfig())
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

$viewTemplateRenderer = new ViewTemplateRenderer($viewRendererConfig);
```

### 3.4) Custom View Renderer modules

By default, the `ViewTemplateRenderer` creates module instances using classes from the current package, including the
Blade compiler.

If you need to override the default module behavior, you can define a custom implementation in the
configuration. The `ViewTemplateRenderer` will use the specified implementation.

> Tip: You can see the full list of the modules in the `ViewTemplateRendererModules`.

#### Example: Overriding the default Blade compiler to use plain PHP views

```php
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\View\ViewTemplateRendererConfig;
use Prosopo\Views\Views;

class CompilerStubForPlainPhpSupport implements TemplateCompilerInterface
{
    public function compileTemplate(string $template): string
    {
        return $template;
    }
}

// ...

$viewTemplateRendererConfig = new ViewTemplateRendererConfig();
$viewTemplateRendererConfig->getModules()
    ->setTemplateCompiler(new CompilerStubForPlainPhpSupport());

$viewTemplateRenderer = new ViewTemplateRenderer($viewTemplateRendererConfig);

$views = new Views();

$viewNamespaceConfig = new ViewNamespaceConfig($viewTemplateRenderer);
$viewNamespaceConfig
    ->setTemplatesRootPath(__DIR__ . './templates')
    ->setTemplateFileExtension('.php');

$views->addNamespace('MyApp\Models', $viewNamespaceConfig);
```

Now this namespace is configured to deal with plain PHP template files, while having all the package features, including
model-driven approach and template error handling.

## 4. Benchmark

We conducted a [PHP performance benchmark](https://github.com/prosopo/php-views/blob/main/benchmark/src/Benchmark.php)
to compare this package with the Laravel's Blade (mocked using [jenssegers/blade](https://github.com/jenssegers/blade))
and [Twig](https://twig.symfony.com/). Here are the results for 1000x renders:

| Contestant                             | First Rendering, MS | Cached Rendering, MS |
|----------------------------------------|---------------------|----------------------|
| `prosopo/views` (without models)       | 19.75               | 19.75 (no cache atm) |
| `prosopo/views` (with models)          | 43.78               | 43.78 (no cache atm) |
| `illuminate/view` (Blade from Laravel) | 181.24              | 56.77 ms             |
| `twig/twig`                            | 441.13              | 9.47 ms              |

We used the following package versions:

* [illuminate/view](https://packagist.org/packages/illuminate/view) `11.7.0`
* [twig/twig](https://packagist.org/packages/twig/twig) `3.17.1`
* [jenssegers/blade](https://packagist.org/packages/jenssegers/blade) `2.0.1`

Since the [benchmark](https://github.com/prosopo/php-views/blob/main/benchmark/src/Benchmark.php) is included in this
repository, you can easily run it locally to verify the results.

1. `git clone https://github.com/prosopo/php-views.git`
2. `composer install; cd benchmark; composer install`
3. `php benchmark {1000}` - pass your renders count

We encourage you to enhance the benchmark further - feel free to make it more advanced and submit a pull request. We're
happy to review and accept contributions! 🚀

## 5. Contribution

We would be excited if you decide to contribute! Please read
the [for-devs.md](https://github.com/prosopo/php-views/blob/main/for-devs.md) file for project guidelines and
agreements.

## 6. Credits

This package was created by [Maxim Akimov](https://github.com/light-source/) during the development of
the [WordPress integration for Prosopo Procaptcha](https://wordpress.org/plugins/prosopo-procaptcha/).

[Procaptcha](https://prosopo.io/) is a privacy-friendly and cost-effective alternative to Google reCaptcha.

Consider using the Procaptcha service to protect your privacy and support the Prosopo team.
