<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class ResourceAction
{
    /**
     * @param string $actionName
     * @param string $commandClass
     * @param string|null $description
     */
    public function __construct(
        public string $actionName,
        public string $commandClass,
        public ?string $description = null
    ) {
        // No further checks are performed here because the definition will always be used to create a ResourceAction.
        // The ResourceAction is validated while constructed.
    }
}
