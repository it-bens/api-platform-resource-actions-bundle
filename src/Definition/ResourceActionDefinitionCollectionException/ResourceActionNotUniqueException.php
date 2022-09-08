<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinitionCollectionException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceActionNotUniqueException extends Exception implements CompileTimeExceptionInterface
{
    /**
     * @param string $resource
     * @param string $action
     * @return ResourceActionNotUniqueException
     */
    public static function create(string $resource, string $action): ResourceActionNotUniqueException
    {
        return new self(
            sprintf('The action "%s" is defined at least twice for the resource "%s".', $action, $resource)
        );
    }
}
