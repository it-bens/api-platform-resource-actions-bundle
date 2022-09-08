<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceHasNoShortNameException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $resource
     * @return ResourceHasNoShortNameException
     */
    public static function create(string $resource): ResourceHasNoShortNameException
    {
        return new self(sprintf('The action resource "%s" has no short name in Api Platform.', $resource));
    }
}
