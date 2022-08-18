<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandAssociationException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class CommandBlankException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @return CommandBlankException
     */
    public static function create(): CommandBlankException
    {
        return new self('The resource-action to command association command must not be blank.');
    }
}