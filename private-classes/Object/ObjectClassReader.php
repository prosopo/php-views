<?php

declare(strict_types=1);

namespace Prosopo\Views\PrivateClasses\Object;

/**
 * This class is an internal one, and placed under the 'Private' namespace to prevent anyone from using it directly.
 * We reserve the right to change its name and implementation.
 *
 * Notes:
 * 1. It's the get_class() wrapper to allow mocking it in tests.
 * 2. It doesn't implement any interface, because it's a solely internal class.
 */
class ObjectClassReader
{
    public function getObjectClass(object $object): string
    {
        return get_class($object);
    }
}
