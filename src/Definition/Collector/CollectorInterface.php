<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Definition\Collector;

use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinition;

interface CollectorInterface
{
    /**
     * @return ResourceActionDefinition[]
     */
    public function getResourceActionDefinitions(): array;
}
