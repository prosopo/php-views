# PHP Views

Blazing fast PHP Views with object Models and standalone Blade as a default template engine.

Benefits:

* Zero Dependencies: Lightweight and easy to integrate into any project.
* Wide Compatibility: PHP 7.4+
* [SOLID principles](https://en.wikipedia.org/wiki/SOLID): Built with maintainability and scalability in mind.
* Test Coverage: Covered by [Pest](https://pestphp.com/) Unit and Feature tests.
* Static Analysis: Checked by [PHPStan](https://phpstan.org/).

Flexible Usage;

You're free to use the package in your own way:

* Use it as a Views provider, combining object Models with the standalone Blade.
* Employ it as a standalone [Blade](https://laravel.com/docs/11.x/blade) implementation for your templates.
* Leverage its object Models wrapper for any template engine (e.g., [Twig](https://twig.symfony.com/)).

## 1. Template Renderer

### 1.1) Blade standalone (build-in)

[Blade](https://laravel.com/docs/11.x/blade) is an elegant and powerful template engine originally designed
for [Laravel](https://laravel.com/).

However, since it isn't available as a
standalone package, this package includes its own Blade compiler.

It provides full support for [Blade's key features](https://laravel.com/docs/11.x/blade)
while remaining completely independent of Laravel.

#### Zero setup

```php
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\Blade\BladeRendererConfig;

$bladeRenderer = new BladeTemplateRenderer(new BladeRendererConfig());

echo $bladeRenderer->renderTemplate('@if($var)The variable is set.@endif', [
    'var' => true
]);
```

#### All Blade Renderer settings

```php
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\Interfaces\Template\TemplateErrorInterface;

$bladeConfig = new BladeRendererConfig();

// Template error handler, can be used for logging, notifying, etc.
$bladeConfig->setTemplateErrorHandler(function (TemplateErrorInterface $templateError): void {
    $templateError->getTemplate();
    $templateError->getCompiledPhpTemplate();
    $templateError->getLine();
    $templateError->getMessage();
    $templateError->getVariables();
});

// Custom output escape callback.
$bladeConfig->setCustomOutputEscapeCallback(function ($variable): string {
    if (
        false === is_string($variable) &&
        false === is_numeric($variable)
    ) {
        return '';
    }

    // htmlspecialchars is the default one.
    return htmlentities((string)$variable, ENT_QUOTES, 'UTF-8', false);
});

// Global functions and variables which will be available inside all the blade templates.
$bladeConfig->setGlobalVariables([
    'sum' => function (int $a, int $b): string {
        return (string)($a + $b);
    },
    'variable' => 'value',
]);

// Escape variable name. Wraps output in the compiled PHP template, like '$escape()'.
$bladeConfig->setEscapeVariableName('escape');

// Compiler extension callback, so you can add your own directives.
$bladeConfig->setCompilerExtensionCallback(function (string $template): string {
    // note: just an example, @use is supported by default.
    $template = (string)preg_replace('/@use\s*\((["\'])(.*?)\1\)/s', '<?php use $2; ?>', $template);
    
    return $template;
});
```

**Standalone Blade Note**: You may have come across packages that attempt to adapt the official Blade engine by creating
stubs for its Laravel dependencies, such as the [jenssegers/blade](https://github.com/jenssegers/blade) package.
However, we chose not to adopt this approach for several reasons:

* PHP Version Requirements: It mandates PHP 8.2 or higher.
* External Dependencies: It introduces additional external dependencies.
* Potential Breakage: It can become unstable with future Laravel updates (as demonstrated
  by [past incidents](https://github.com/jenssegers/blade/issues/74).
* Limited Flexibility: Since it wasn’t designed as a standalone component, it lacks features like a customizable
  template error handler.

Thanks to great Blade's conceptual design, our compiler implementation required fewer than 200 lines of code.

### 1.2) Custom template renderer provider (e.g. Twig)

```php
use Prosopo\Views\Interfaces\Template\TemplateRendererInterface;

class TwigTemplateRenderer implements TemplateRendererInterface {
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
```

## 2. View model

### Flat setup

```php
namespace MyPackage\Views;

use Prosopo\Views\Interfaces\View\ViewInterface;

class MyView extends \Prosopo\Views\View\View
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

Model benefits:

1. Typed variables: Eliminate the hassle of type matching and renaming associated with array-driven variables.
2. Reduced Routine: During object creation, public fields of the model without default values are automatically
   initialized with default values.
3. Enhanced Access: Public methods are made available to the template alongside the variables.
4. Unified Interface:  Use the `ViewInterface` in your application when accepting or returning a `View` to maintain
   flexibility and avoid specifying the exact component.

The `View` class implements the `View_Interface`. During rendering, any inner objects that also implement
`View_Interface` will be automatically rendered and passed into the template as strings.

Note: In the `View` class, in order to satisfy the Model creator, the constructor is marked as final. If you need to set
custom default values, consider using one of the following approaches:

```php
namespace MyPackage\Views;

class MyView extends \Prosopo\Views\View\View
{
    // approach for plain field types
    public int $varWithCustomValue = 'custom default value';

    protected function setCustomDefaults(){
        // approach for object field types
        $this->company = new Company();
    }
}
```

### Custom component

The only requirement for a component is to implement the `ViewInterface`. This means you can transform any class into a
`View` without needing to extend a specific base class, allowing for maximum flexibility and customization.

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

## 3. Views

### 3.1) Setup

```php
use Prosopo\Views\Blade\BladeRendererConfig;
use Prosopo\Views\Blade\BladeTemplateRenderer;
use Prosopo\Views\ViewsConfig;
use Prosopo\Views\Views;

// You can use the build-in Blade Renderer, 
// or wrap any template engine as shown in the first chapter.
$renderer = new BladeTemplateRenderer(new BladeRendererConfig());

$viewsConfig = new ViewsConfig();

$viewsConfig->setTemplatesRootPath(__DIR__ . './templates');
$viewsConfig->setViewsRootNamespace('MyPackage\Views');
$viewsConfig->setTemplateFileExtension('.blade.php');
$viewsConfig->setTemplateRenderer($renderer);

// fixme add errorHandler that contains the View object.

$views = new Views($viewsConfig);
```

The built-in `TemplateProvider` automatically matches templates based on the Model names and their relative namespaces.
This automates the process of associating templates with their corresponding Models.

Example:

- src/
    - Views/
        - MyView.php
        - settings
            - GeneralSettings.php
    - templates/
        - my-view.blade.php
        - settings/
            - general-settings.blade.twig

**Naming Note:** Use dashes in template names, as camelCase in Model names is automatically converted to dash-separated
names.

### 3.2) Single-step creation and rendering

You can create, set values, and render a Model in a single step using the callback argument of the renderView method, as
shown below.

```php
echo $views->getRenderer()
    ->renderView(MyView::class, function (MyView $view) use ($salary, $bonus) {
        $view->salary = $salary;
        $view->bonus = $bonus;
    });
```

This approach enables a functional programming style when working with Models.

### 3.3) Multi-step creation and rendering

When you need split creation, use the factory to create the model, and then render when you need it.

```php
$view = $views->getFactory()
    ->makeView(MyView::class);

// ...

$view->salary = $salary;
$view->bonus = $bonus;

// ...

echo $views->getRenderer()
    ->renderView($view);
```

## Contribution

We would be excited if you decide to contribute! Please read the `for-devs.md` file for project guidelines and
agreements.

## Credentials

This package was created by [Maxim Akimov](https://github.com/light-source/) during the development of
the [WordPress integration for Prosopo Procaptcha](https://wordpress.org/plugins/prosopo-procaptcha/).

[Procaptcha](https://prosopo.io/) is a privacy-friendly and cost-effective alternative to Google reCaptcha.

Consider using the Procaptcha service to protect your privacy and support the Prosopo team.
