<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs\OpenApiFactoryException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class PathNotfoundException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $apiPath
     * @return PathNotfoundException
     */
    public static function create(string $apiPath): PathNotfoundException
    {
        return new self(sprintf('The API path "%s" could not be found by OpenAPI.', $apiPath));
    }
}
