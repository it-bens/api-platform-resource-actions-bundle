<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Action\ActionException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class CommandNotAClassException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $commandClass
     * @return CommandNotAClassException
     */
    public static function create(string $commandClass): CommandNotAClassException
    {
        return new self(sprintf('The action command class "%s" is not a valid class name.', $commandClass));
    }
}
