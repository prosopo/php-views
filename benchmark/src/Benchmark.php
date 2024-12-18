<?php

declare(strict_types=1);

namespace Benchmark;

use Exception;
use Jenssegers\Blade\Blade;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\ViewsManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Benchmark
{
    public function run(): void
    {
        $rootDir = __DIR__ . '/../tmp';

        mkdir($rootDir);

        $templatesCount = $_SERVER['argc'] > 1 ?
            (int)$_SERVER['argv'][1] :
            1000;
        $templateNameLength = 10;
        $itemsInTemplateCount = 100;
        $templateArguments = [
            'items' => array_fill(0, $itemsInTemplateCount, 'item'),
        ];
        $validationString = sprintf('[%s]', $itemsInTemplateCount);

        list($twigSpentMs, $twigSpentMsWithCache) = $this->benchmarkForTwig(
            $rootDir,
            $this->getTestTemplate('.twig'),
            $templatesCount,
            $templateNameLength,
            $templateArguments,
            $validationString
        );

        list($bladeOriginSpentMs, $bladeOriginSpentMsWithCache) = $this->benchmarkForBladeOrigin(
            $rootDir,
            $this->getTestTemplate('.blade.php'),
            $templatesCount,
            $templateNameLength,
            $templateArguments,
            $validationString
        );

        $phpViewsSpentMs = $this->benchmarkForPhpViews(
            $rootDir,
            $this->getTestTemplate('.blade.php'),
            $templatesCount,
            $templateNameLength,
            $templateArguments,
            $validationString
        );

        $phpViewsWithModelsSpentMs = $this->benchmarkForPhpViewsWithModels(
            $rootDir,
            $this->getTestTemplate('.blade.php'),
            $templatesCount,
            $templateNameLength,
            $templateArguments,
            $validationString
        );

        $results = [
            'Blade from Laravel (raw)' => $bladeOriginSpentMs,
            'Blade from Laravel (with cache)' => $bladeOriginSpentMsWithCache,
            'PHP Views with Models (built-in Blade)' => $phpViewsWithModelsSpentMs,
            'PHP Views without Models (using built-in Blade)' => $phpViewsSpentMs,
            'Twig (raw)' => $twigSpentMs,
            'Twig (with cache)' => $twigSpentMsWithCache,
        ];

        // sort asc
        asort($results);

        printf("Renders Count: %sx\n", $templatesCount);

        array_walk($results, function ($spentMs, $contestant) {
            printf("%s ms: %s\n", $spentMs, $contestant);
        });

        $this->removeDir($rootDir);
    }

    public function benchmarkForBladeOrigin(
        string $rootDir,
        string $template,
        int $templatesCount,
        int $templateNameLength,
        array $templateArguments,
        string $validationString
    ): array {
        $bladeOriginDir = $rootDir . '/origin-blade';
        $bladeOriginCacheDir = $rootDir . '/origin-blade-cache';

        mkdir($bladeOriginDir);
        mkdir($bladeOriginCacheDir);

        $blade = new Blade($bladeOriginDir, $bladeOriginCacheDir);

        $bladeFiles = $this->writeUniqueTemplates(
            $bladeOriginDir,
            '.blade.php',
            $template,
            $templatesCount,
            $templateNameLength,
            true
        );

        $calc = fn()=> $this->measureFileRenders(
            'blade-origin',
            function ($templateFile) use ($blade, $templateArguments) {
                return $blade->render($templateFile, $templateArguments);
            },
            $bladeFiles,
            $validationString
        );

        return [$calc(),$calc()];
    }

    protected function benchmarkForPhpViews(
        string $rootDir,
        string $template,
        int $templatesCount,
        int $templateNameLength,
        array $templateArguments,
        string $validationString
    ): float {
        $phpViewsDir = $rootDir . '/php-views';

        mkdir($phpViewsDir);

        $phpViewsRenderer = new ViewTemplateRenderer();

        $phpViewsFiles = $this->writeUniqueTemplates(
            $phpViewsDir,
            '.blade.php',
            $template,
            $templatesCount,
            $templateNameLength,
            false
        );

        return $this->measureFileRenders(
            'php-views',
            function ($templateFile) use ($phpViewsRenderer, $templateArguments) {
                return $phpViewsRenderer->renderTemplate($templateFile, $templateArguments);
            },
            $phpViewsFiles,
            $validationString
        );
    }

    protected function benchmarkForPhpViewsWithModels(
        string $rootDir,
        string $template,
        int $templatesCount,
        int $templateNameLength,
        array $templateArguments,
        string $validationString
    ): float {
        $phpViewsWithModelsDir = $rootDir . '/php-views-with-models';

        mkdir($phpViewsWithModelsDir);

        $phpViewsRenderer = new ViewTemplateRenderer();
        $phpViews = new ViewsManager();

        $namespaceConfig = new ViewNamespaceConfig($phpViewsRenderer);
        $namespaceConfig->setTemplateFileExtension('.blade.php');
        $namespaceConfig->setTemplatesRootPath($phpViewsWithModelsDir);

        $namespace = '_php_views_with_models';

        $phpViews->registerNamespace($namespace, $namespaceConfig);

        $phpViewsWithModelFiles = $this->writeUniqueTemplates(
            $phpViewsWithModelsDir,
            '.blade.php',
            $template,
            $templatesCount,
            $templateNameLength,
            true
        );

        return $this->measureFileRenders(
            'php-views-with-models',
            function ($templateFile) use ($namespace, $phpViews, $templateArguments) {

                $modelClass = $this->defineModelClass($namespace, $templateFile);

                return $phpViews->renderModel($modelClass, function ($model) use ($templateArguments) {
                    $model->items = $templateArguments['items'];
                });
            },
            $phpViewsWithModelFiles,
            $validationString
        );
    }

    protected function benchmarkForTwig(
        string $rootDir,
        string $template,
        int $templatesCount,
        int $templateNameLength,
        array $templateArguments,
        string $validationString
    ): array {
        $twigDir = $rootDir . '/twig';
        $twigCacheDir = $rootDir . '/twig-cache';

        mkdir($twigDir);

        $twigLoader = new FilesystemLoader($twigDir);
        $twig = new Environment($twigLoader, [
            'cache' => $twigCacheDir,
        ]);

        $twigFiles = $this->writeUniqueTemplates(
            $twigDir,
            '.twig',
            $template,
            $templatesCount,
            $templateNameLength,
            true
        );

        $calc = fn() => $this->measureFileRenders(
            'twig',
            function ($templateFile) use ($twig, $templateArguments) {
                return $twig->render($templateFile . '.twig', $templateArguments);
            },
            $twigFiles,
            $validationString
        );

        return [$calc(),$calc()];
    }

    protected function defineModelClass(string $namespace, string $className): string
    {
        $code = sprintf(
            'namespace %s; class %s extends \Prosopo\Views\TemplateModel { public array $items;  }',
            $namespace,
            $className
        );

        eval($code);

        return $namespace . '\\' . $className;
    }

    protected function removeDir(string $directory): void
    {
        if (false === is_dir($directory)) {
            return;
        }

        $fsItems = scandir($directory);

        array_walk($fsItems, function ($item) use ($directory) {
            if (
                $item === '.' ||
                $item === '..'
            ) {
                return;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;

            if (true === is_dir($path)) {
                $this->removeDir($path);
            } else {
                unlink($path);
            }
        });

        rmdir($directory);
    }

    protected function writeUniqueTemplates(
        string $rootPath,
        string $extension,
        string $template,
        int $count,
        int $nameLength,
        bool $isNameOnly
    ): array {
        $randomStrings = $this->getRandomUniqueStrings($count, $nameLength);

        return array_reduce(
            $randomStrings,
            function ($templateFiles, $randomString) use ($rootPath, $extension, $template, $isNameOnly) {
                $templateFile = $rootPath . '/' . $randomString . $extension;
                // add name to the content to avoid any potential content-related cache.
                $templateContent = $template . ' ' . $randomString;

                file_put_contents($templateFile, $templateContent);

                $templateFiles[] = true === $isNameOnly ?
                    $randomString :
                    $templateFile;

                return $templateFiles;
            },
            []
        );
    }

    protected function getSpentTimeInMilliseconds(float $start): float
    {
        $spendTime = (microtime(true) - $start) * 1000;

        return round($spendTime, 2);
    }

    protected function measureFileRenders(
        string $vendor,
        callable $render,
        array $templateFiles,
        string $validationString
    ): float {
        $start = microtime(true);

        array_walk($templateFiles, function ($templateFile) use ($vendor, $render, $validationString) {
            $rendered = $render($templateFile);

            if (false === strpos($rendered, $validationString)) {
                throw new Exception('Unexpected render result for ' . $vendor . ' (' . $templateFile . ')');
            }
        });

        return $this->getSpentTimeInMilliseconds($start);
    }

    protected function getRandomUniqueStrings(int $count, int $stringLength, array $uniqueStrings = []): array
    {
        if (count($uniqueStrings) >= $count) {
            return array_keys($uniqueStrings);
        }

        $randomString = $this->getRandomString($stringLength);
        $uniqueStrings[$randomString] = true;

        return $this->getRandomUniqueStrings($count, $stringLength, $uniqueStrings);
    }

    protected function getRandomString(int $length): string
    {
        $characters = array_merge(
            range('a', 'z'),
        );

        $charactersLength = count($characters);
        $initialArray = array_fill(0, $length, null);

        $randomLetters = array_map(function () use ($characters, $charactersLength) {
            $index = random_int(0, $charactersLength - 1);

            return $characters[$index];
        }, $initialArray);

        return implode('', $randomLetters);
    }

    protected function getTestTemplate(string $extension): string
    {
        $file = __DIR__ . '/../template/template' . $extension;

        if (false === file_exists($file)) {
            throw new Exception('Template file not found');
        }

        return (string)file_get_contents($file);
    }
}
