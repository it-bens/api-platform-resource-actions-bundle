<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs\OpenApiFactoryException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class CommandClassConstructorNull extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $commandClass
     * @return CommandClassConstructorNull
     */
    public static function create(string $commandClass): CommandClassConstructorNull
    {
        return new self(
            sprintf(
                'The command class "%s" has no constructor. A constructor is required to infer the payload properties.',
                $commandClass
            )
        );
    }
}