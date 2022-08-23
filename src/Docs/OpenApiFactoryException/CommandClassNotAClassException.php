<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs\OpenApiFactoryException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;
use ReflectionException;

final class CommandClassNotAClassException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $commandClass
     * @param ReflectionException|null $exception
     * @return CommandClassNotAClassException
     */
    public static function create(
        string $commandClass,
        ?ReflectionException $exception = null
    ): CommandClassNotAClassException {
        return new self(
            sprintf('The command class "%s" is not a valid class.', $commandClass),
            previous: $exception
        );
    }
}