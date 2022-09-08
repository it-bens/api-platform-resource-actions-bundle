<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Definition;

use ITB\ApiPlatformResourceActionsBundle\Attribute\ResourceAction;

final class ResourceActionDefinition
{
    /**
     * @param string $resource
     * @param string $action
     * @param string $command
     * @param string|null $description
     */
    public function __construct(
        public string $resource,
        public string $action,
        public string $command,
        public ?string $description
    ) {
        // No further checks are performed here because the definition will always be used to create a ResourceAction.
        // The ResourceAction is validated while constructed.
    }

    /**
     * @param ResourceAction $resourceAction
     * @param string $resourceClass
     * @return ResourceActionDefinition
     */
    public static function fromResourceActionAttribute(
        ResourceAction $resourceAction,
        string $resourceClass
    ): ResourceActionDefinition {
        return new self(
            $resourceClass,
            $resourceAction->actionName,
            $resourceAction->commandClass,
            $resourceAction->description
        );
    }
}
