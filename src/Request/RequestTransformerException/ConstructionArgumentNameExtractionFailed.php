<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use ReflectionException;
use Throwable;

final class ConstructionArgumentNameExtractionFailed extends Exception implements RuntimeExceptionInterface
{
    /**
     * @param string $commandClass
     * @param string $objectClass
     * @param ReflectionException $exception
     * @return ConstructionArgumentNameExtractionFailed
     */
    public static function create(
        string $commandClass,
        string $objectClass,
        ReflectionException $exception
    ): ConstructionArgumentNameExtractionFailed {
        return new self(
            sprintf(
                'The extraction of the constructor argument of type %s for command class %s failed.',
                $objectClass,
                $commandClass
            ), previous: $exception
        );
    }

    public function createApiPlatformCompatibleException(): Throwable
    {
        // TODO: Implement createApiPlatformCompatibleException() method.
    }
}