<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs\OpenApiFactoryException;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;
use Throwable;

final class ResourceActionDocumentationFailedException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $resource
     * @param string $action
     * @param ResourceClassNotFoundException $exception
     * @return ResourceActionDocumentationFailedException
     */
    public static function create(
        string $resource,
        string $action,
        Throwable $exception
    ): ResourceActionDocumentationFailedException {
        return new self(
            sprintf('The action "%s" of resource "%s" could not be documented.', $action, $resource),
            previous: $exception
        );
    }
}