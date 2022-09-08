<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;

final class NameBlankException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @return NameBlankException
     */
    public static function create(): NameBlankException
    {
        return new self('The action name must not be blank.');
    }
}
