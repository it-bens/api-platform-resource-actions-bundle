<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Context\ApiPlatformContextException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use Throwable;

final class ResourceClassNotAStringException extends Exception implements RuntimeExceptionInterface
{
    /**
     * @return ResourceClassNotAStringException
     */
    public static function create(): ResourceClassNotAStringException
    {
        return new self('The \'resource_class\' from the Api Platform deserialization context is not a string.');
    }

    /**
     * @return Throwable
     */
    public function createApiPlatformCompatibleException(): Throwable
    {
        // TODO: Implement createApiPlatformCompatibleException() method.
    }
}