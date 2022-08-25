<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action\ActionException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;

final class CommandHasNoConstructorException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $commandClass
     * @return CommandHasNoConstructorException
     */
    public static function create(string $commandClass): CommandHasNoConstructorException
    {
        return new self(sprintf('The action command class "%s" has no .', $commandClass));
    }
}
