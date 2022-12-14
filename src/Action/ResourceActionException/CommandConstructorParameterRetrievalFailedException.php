<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ReflectionException;
use Throwable;

final class CommandConstructorParameterRetrievalFailedException extends Exception implements RuntimeExceptionInterface
{
    /**
     * @param string $commandClass
     * @param string $type
     * @param ReflectionException $exception
     * @return CommandConstructorParameterRetrievalFailedException
     */
    public static function create(
        string $commandClass,
        string $type,
        ReflectionException $exception
    ): CommandConstructorParameterRetrievalFailedException {
        return new self(
            sprintf(
                'A parameter of type "%s" could not be extracted from action command class "%s".',
                $type,
                $commandClass
            ),
            previous: $exception
        );
    }

    /**
     * @return Throwable
     */
    public function createApiPlatformCompatibleException(): Throwable
    {
        // TODO: Implement createApiPlatformCompatibleException() method.
        return new Exception();
    }
}
