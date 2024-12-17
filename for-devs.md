# For Devs

This file provides information about the project and serves as a guide for those interested in contributing.

## 1. Architecture Guidelines

The codebase adheres to the [SOLID principles](https://en.wikipedia.org/wiki/SOLID). Please keep these principles in
mind when introducing new classes or modules to ensure consistency and maintainability.

## 2. Code Style

The project follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard. Configure your IDE to use
the [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) and use the `code-quality/phpcs.xml` config, or run
the following command to automatically fix style issues:

`cd code-quality; composer install; composer phpcbf`

## 3. Static Analysis

The project uses [PHPStan](https://phpstan.org/) for static analysis. Configure your IDE with the
`code-quality/phpstan.neon` config or run the analysis using the following command:

`cd code-quality; composer install; composer phpstan`

## 4. Tests

The tests are powered by [Pest](https://pestphp.com/). To run the tests:

`cd tests; composer install; composer pest`

## 5. Pull Requests

Please open your Pull Requests against the `main` branch.