<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Template;

use Prosopo\Views\Interfaces\EventDispatcherInterface;
use Prosopo\Views\Interfaces\Template\FileTemplateContentProviderInterface;

/**
 * This class is marked as a final and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 */
final class FileTemplateContentProvider implements FileTemplateContentProviderInterface
{
    private string $errorEventName;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        string $errorEventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->errorEventName = $errorEventName;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getFileTemplateContent(string $file): string
    {
        if (file_exists($file)) {
            // @phpcs:ignore
            return (string)file_get_contents($file);
        }

        $this->eventDispatcher->dispatchEvent($this->errorEventName, [
            'error' => 'Template file does not exist',
            'file' => $file,
        ]);

        return '';
    }
}
