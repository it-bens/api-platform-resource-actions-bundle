<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Action\ActionException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class CommandBlankException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @return CommandBlankException
     */
    public static function create(): CommandBlankException
    {
        return new self('The action command class must not be blank.');
    }
}
