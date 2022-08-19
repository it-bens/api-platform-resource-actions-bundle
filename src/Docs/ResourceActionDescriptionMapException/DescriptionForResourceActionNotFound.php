<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs\ResourceActionDescriptionMapException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class DescriptionForResourceActionNotFound extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $resource
     * @param string $action
     * @return DescriptionForResourceActionNotFound
     */
    public static function create(string $resource, string $action): DescriptionForResourceActionNotFound
    {
        return new self(
            sprintf(
                'There is no associated description for the API Platform resource "%s" and the action "%s".',
                $resource,
                $action
            )
        );
    }
}