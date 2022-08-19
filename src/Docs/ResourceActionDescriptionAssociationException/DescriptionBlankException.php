<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs\ResourceActionDescriptionAssociationException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class DescriptionBlankException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @return DescriptionBlankException
     */
    public static function create(): DescriptionBlankException
    {
        return new self('The resource-action to description association description must not be blank.');
    }
}