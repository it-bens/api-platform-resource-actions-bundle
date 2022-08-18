<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandAssociationException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceBlankException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @return ResourceBlankException
     */
    public static function create(): ResourceBlankException
    {
        return new self('The resource-action to command association resource must not be blank.');
    }
}