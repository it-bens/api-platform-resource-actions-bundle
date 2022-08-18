<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandAssociationException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class ActionBlankException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @return ActionBlankException
     */
    public static function create(): ActionBlankException
    {
        return new self('The resource-action to command association action must not be blank.');
    }
}