<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;

final class NoOperationConfiguredForActionException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $actionName
     * @param string $resource
     * @return NoOperationConfiguredForActionException
     */
    public static function create(string $actionName, string $resource): NoOperationConfiguredForActionException
    {
        return new self(
            sprintf(
                'There is no operation configured in API Platform for resource "%s" to use the action "%s".',
                $resource,
                $actionName
            )
        );
    }
}
