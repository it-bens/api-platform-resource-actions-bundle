<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandMapException;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceNotRegisteredException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $resource
     * @param ResourceClassNotFoundException $exception
     * @return ResourceNotRegisteredException
     */
    public static function create(string $resource, ResourceClassNotFoundException $exception): ResourceNotRegisteredException
    {
        return new self(
            sprintf(
                'The resource-action to command association resource "%s" is not registered via Api Platform.',
                $resource
            ), previous: $exception
        );
    }
}