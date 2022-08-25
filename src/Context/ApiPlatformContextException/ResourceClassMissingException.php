<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use Throwable;

final class ResourceClassMissingException extends Exception implements RuntimeExceptionInterface
{
    /**
     * @return ResourceClassMissingException
     */
    public static function create(): ResourceClassMissingException
    {
        return new self('The \'resource_class\' key is missing from the Api Platform deserialization context.');
    }

    /**
     * @return Throwable
     */
    public function createApiPlatformCompatibleException(): Throwable
    {
        // TODO: Implement createApiPlatformCompatibleException() method.
    }
}
