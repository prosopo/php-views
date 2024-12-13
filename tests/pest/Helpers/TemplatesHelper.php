<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Exception;

class TemplatesHelper
{
    /**
     * @param string[] $paths
     *
     * @return string[]
     */
    public function getTemplatesByExtension(string $extension, array $paths): array
    {
        return array_reduce(
            $paths,
            function (array $templates, string $path) use ($extension) {
                return array_merge($templates, $this->getFilesByExtension($extension, $path));
            },
            []
        );
    }

    public function getTemplate(string $file): string
    {
        if (false === file_exists($file)) {
            throw new Exception('Template is not found: ' . $file);
        }

        return (string) file_get_contents($file);
    }

    /**
     * @return string[]
     */
    protected function getFilesByExtension(string $fileExtension, string $directory): array
    {
        if (
            false === is_dir($directory) ||
            false === is_readable($directory)
        ) {
            return array();
        }

        $files = scandir($directory);

        if (false === $files) {
            return array();
        }

        $fileNames = array_filter(
            $files,
            function (string $file) use ($directory, $fileExtension) {
                $filePath = $directory . DIRECTORY_SEPARATOR . $file;

                return true === is_file($filePath) &&
                        substr($file, -strlen($fileExtension)) === $fileExtension;
            }
        );

        return array_map(
            fn(string $fileName) => $directory . DIRECTORY_SEPARATOR . str_replace($fileExtension, '', $fileName),
            $fileNames
        );
    }
}
