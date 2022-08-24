<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Action\ActionException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

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
