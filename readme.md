# PHP Views

Blazing fast PHP Views with model-driven approach and built-in [Blade](https://laravel.com/docs/11.x/blade)
implementation as a default template engine.

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

## 1. Model-driven approach

Similar to many frameworks, such as Laravel, this package embraces a model-driven approach to templates. Each template
is paired with its own Model, where the Model's public properties and methods act as arguments available within the
template.

Model class:

```php
namespace MyPackage\Views;

use Prosopo\Views\Interfaces\View\ViewInterface;
use Prosopo\Views\View;

class MyView extends View
{
    public int $salary;
    public int $bonus;
    public View_Interface $innerComponent;
    // Use this specific class only when you want to restrict the usage to it.
    public CompanyView $company;

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

{!! $innerComponent !!}

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

use Prosopo\Views\View;

class MyView extends View
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

> Tip: If your app Models require additional object dependencies, you can override the `InstancePropertyProvider`
> module to
> integrate with a Dependency Injection container like [PHP-DI](https://php-di.org/). This allows class properties to be
> automatically resolved
> while object creation by your application's DI system.

### 1.4) Custom Model implementation (advanced usage)

The only requirement for a View class is to implement the `ViewInterface`. This means you can transform any class into a
`View` without needing to extend a specific base class, allowing for maximum flexibility and customization:

```php
namespace MyPackage\Views;

use Prosopo\Views\Interfaces\View\ViewInterface;

class MyView implements ViewInterface {
    public int $salary;
    public int $bonus;

    public function total(): int
    {
        return $this->salary + $this->bonus;
    }
    
    // ViewInterface requires only the single method:
    
    public function getTemplate(): string {
        return '@if($total() > 3000)Congrats, you have a great salary!@endif';
    }
}
```

## 2. Views

`Views` is the root class of this package, responsible for initializing all modules, including the `TemplateProvider`,
which automates the linking of Models to their respective templates.

### 2.1) Flat setup

```php
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\Views;
use Prosopo\Views\NamespaceConfig;

$namespaceConfig = (new NamespaceConfig())
    ->setTemplatesRootPath(__DIR__ . './templates')
    ->setViewsRootNamespace('MyPackage\Views')
    ->setTemplateFileExtension('.blade.php');

$namespaceConfig->getModules()
    // You can use the build-in Blade Renderer, or wrap any template engine as shown in the Custom Modules chapter below.
    ->setTemplateRenderer(new BladeTemplateRenderer(new BladeRendererConfig()));

// fixme add errorHandler that contains the View object.

$views = new Views();
$views->addNamespace($namespaceConfig);
```

### 2.2) Single-step Model creation and rendering

You can create, set values, and render a Model in a single step using the callback argument of the `renderView` method,
as
shown below:

```php
echo $views->getRenderer()
    ->renderView(MyView::class, function (MyView $view) use ($salary, $bonus) {
        $view->salary = $salary;
        $view->bonus = $bonus;
    });

// Tip: pass true to the third renderView() argument to print it without echo.
```

This approach enables a functional programming style when working with Models.

### 2.3) Multi-step creation and rendering

When you need split creation, use the factory to create the model, and then render later when you need it.

```php
$view = $views->getFactory()
    ->makeView(MyView::class);

// ...

$view->salary = $salary;
$view->bonus = $bonus;

// ...

echo $views->getRenderer()
    ->renderView($view);

// Tip: you can still pass the callback as the second renderView() argument
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

If you need to override the default module behavior, you can define your custom implementation in the
configuration. The `Views` class will use your specified implementation.

> Tip: You can see the full list of the modules in the `Modules` class.

#### Example: Using Twig as a Template Renderer (instead of the built-in Blade Renderer)

```php
use Prosopo\Views\Interfaces\Template\TemplateProviderInterface;

class TwigRenderer implements TemplateRendererInterface {
    private $twig;
    
    public function __construct() {
        // todo init Twig or another engine.
    }

      /**
     * @param array<string,mixed> $variables
     */
    public function renderTemplate(string $template, array $variables, bool $doPrint = false): string {
        $this->twig->render($template, $variables, $doPrint);
    }
}

$viewsConfig = (new ViewsConfig())
    ->setTemplatesRootPath(__DIR__ . './templates')
    ->setViewsRootNamespace('MyPackage\Views')
    ->setTemplateFileExtension('.twig');

$viewsConfig->getModules()
    ->setTemplateRenderer(new TwigRenderer());

$views = new Views($viewsConfig)
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

## 3. Build-in standalone Blade implementation

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

$bladeRenderer = new BladeTemplateRenderer(new BladeRendererConfig());

echo $bladeRenderer->renderTemplate('@if($var)The variable is set.@endif', [
    'var' => true
]);
```

> Tip: The built-in Blade implementation is fully standalone and independent of the `Views` class. This means that even
> if
> you’re unable to use the model-driven approach, you can still utilize it as an independent Blade compiler.

### 3.3) Available Blade Renderer settings

The built-in Blade implementation includes a variety of settings that let you customize features such as escaping,
error handling, and more:

```php
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;

$bladeRendererConfig = (new BladeRendererConfig())
->setTemplateErrorHandler(function (TemplateErrorInterface $templateError): void {
    // Can be used for logging, notifying, etc.
    $templateError->getTemplate();
    $templateError->getCompiledPhpTemplate();
    $templateError->getLine();
    $templateError->getMessage();
    $templateError->getVariables();
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

If you need to override the default module behavior, you can define your custom implementation in the
configuration. The `BladeTemplateRenderer` will use your specified implementation.

> Tip: You can see the full list of the modules in the `BladeRendererModules` class.

#### Example: Defining a custom Blade compiler

```php
use Prosopo\Views\Interfaces\Template\TemplateCompilerInterface;

class MyBladeCompiler implements TemplateCompilerInterface {
    public function compile(string $template): string {
       // todo your custom compiler.
    }
}

// ...

$bladeRendererConfig->getModules()
    ->setTemplateCompiler(new TwigTemplateRenderer());
```

> Note: Carefully review the `BladeRendererConfig` settings before creating a custom implementation. For example, in the
> case of the `BladeComplier`, it includes the `compilerExtensionCallback` setting, which allows you to implement a
> custom directive without the need to build a custom compiler from scratch.

## Contribution

We would be excited if you decide to contribute! Please read the `for-devs.md` file for project guidelines and
agreements.

## Credentials

This package was created by [Maxim Akimov](https://github.com/light-source/) during the development of
the [WordPress integration for Prosopo Procaptcha](https://wordpress.org/plugins/prosopo-procaptcha/).

[Procaptcha](https://prosopo.io/) is a privacy-friendly and cost-effective alternative to Google reCaptcha.

Consider using the Procaptcha service to protect your privacy and support the Prosopo team.
