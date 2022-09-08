<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceBlankException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @return ResourceBlankException
     */
    public static function create(): ResourceBlankException
    {
        return new self('The action resource must not be blank.');
    }
}
