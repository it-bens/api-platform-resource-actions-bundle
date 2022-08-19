<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs\OpenApiFactoryException;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceMetadataRetrievalFailedException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $resource
     * @param ResourceClassNotFoundException $exception
     * @return ResourceMetadataRetrievalFailedException
     */
    public static function create(
        string $resource,
        ResourceClassNotFoundException $exception
    ): ResourceMetadataRetrievalFailedException {
        return new self(
            sprintf('The metadata for resource "%s" could not be retrieved from API platform.', $resource),
            previous: $exception
        );
    }
}