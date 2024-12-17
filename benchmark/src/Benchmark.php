<?php

declare(strict_types=1);

namespace Benchmark;

use Exception;
use Jenssegers\Blade\Blade;
use Prosopo\Views\View\ViewNamespaceConfig;
use Prosopo\Views\View\ViewTemplateRenderer;
use Prosopo\Views\Views;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Benchmark
{
    public function run(): void
    {
        $rootDir = __DIR__ . '/../tmp';
        $twigDir = $rootDir . '/twig';
        $bladeOriginDir = $rootDir . '/origin-blade';
        $bladeOriginCacheDir = $rootDir . '/origin-blade-cache';
        $phpViewsDir = $rootDir . '/php-views';
        $phpViewsModelsDir = $rootDir . '/php-views-models';

        mkdir($rootDir);
        mkdir($twigDir);
        mkdir($bladeOriginDir);
        mkdir($bladeOriginCacheDir);
        mkdir($phpViewsDir);

        $twigLoader = new FilesystemLoader($twigDir);
        $twig = new Environment($twigLoader);

        $blade = new Blade($bladeOriginDir, $bladeOriginCacheDir);

        $phpViewsRenderer = new ViewTemplateRenderer();
        $phpViews=new Views();
        $phpViewsNamespace=new ViewNamespaceConfig($phpViewsRenderer);
        $phpViewsNamespace->setTemplatesRootPath($phpViewsModelsDir);
        $phpViewsNamespace->setTemplateFileExtension('.blade.php');
        $phpViews->addNamespace('Test', $phpViewsNamespace);


        $twigTemplate = $this->getTestTemplate('.twig');
        $bladeTemplate = $this->getTestTemplate('.blade.php');

        $templatesCount = 1000;
        $templateNameLength = 10;
        $itemsInTemplateCount = 100;

        $twigFiles = $this->writeUniqueTemplates(
            $twigDir,
            '.twig',
            $twigTemplate,
            $templatesCount,
            $templateNameLength,
            true
        );
        $bladeOriginFiles = $this->writeUniqueTemplates(
            $bladeOriginDir,
            '.blade.php',
            $bladeTemplate,
            $templatesCount,
            $templateNameLength,
            true
        );
        $phpViewFiles = $this->writeUniqueTemplates(
            $phpViewsDir,
            '.blade.php',
            $bladeTemplate,
            $templatesCount,
            $templateNameLength,
            false
        );
        $phpModelFiles = $this->writeUniqueTemplates(
            $phpViewsModelsDir,
            '.blade.php',
            $bladeTemplate,
            $templatesCount,
            $templateNameLength,
            false
        );

        $templateArguments = [
            'items' => array_fill(0, $itemsInTemplateCount, 'item'),
        ];
        $validationString = sprintf('[%s]', $itemsInTemplateCount);

        $twigSpentMs = $this->measureFileRenders(
            'twig',
            function ($templateFile) use ($twig, $templateArguments) {
                return $twig->render($templateFile . '.twig', $templateArguments);
            },
            $twigFiles,
            $validationString
        );

        $bladeOriginSpentMs = $this->measureFileRenders(
            'blade-origin',
            function ($templateFile) use ($blade, $templateArguments) {
                return $blade->render($templateFile, $templateArguments);
            },
            $bladeOriginFiles,
            $validationString
        );

        $phpViewsSpentMs = $this->measureFileRenders(
            'php-views',
            function ($templateFile) use ($phpViewsRenderer, $templateArguments) {
                return $phpViewsRenderer->renderTemplate($templateFile, $templateArguments);
            },
            $phpViewFiles,
            $validationString
        );

        $phpViewsWithModelsSpentMs = $this->measureFileRenders(
            'php-views',
            function ($templateFile) use ($phpViewsRenderer, $templateArguments) {
                // fixme
                return $phpViewsRenderer->renderTemplate($templateFile, $templateArguments);
            },
            $phpViewFiles,
            $validationString
        );

        printf("String Renders: %sx\n", $templatesCount);
        printf("Twig: %s ms\n", $twigSpentMs);
        printf("Blade Origin: %s ms\n", $bladeOriginSpentMs);
        printf("PHP Views with built-in Blade: %s ms\n", $phpViewsSpentMs);
        printf("PHP Views with built-in Blade and Models: %s ms\n", $phpViewsWithModelsSpentMs);

        $this->removeDir($rootDir);
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
            range('0', '9'),
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
