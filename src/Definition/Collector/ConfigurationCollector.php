<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Definition\Collector;

use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinition;

final class ConfigurationCollector implements CollectorInterface
{
    /** @var ResourceActionDefinition[] $definitions */
    private array $definitions = [];

    /**
     * @param array<string, array<string, array{
     *     command_class: class-string,
     *     description: string|null
     * }>> $resourcesConfiguration
     */
    public function __construct(array $resourcesConfiguration)
    {
        foreach ($resourcesConfiguration as $resource => $actions) {
            foreach ($actions as $action => $actionData) {
                $this->definitions[] = new ResourceActionDefinition(
                    $resource,
                    $action,
                    $actionData['command_class'],
                    $actionData['description']
                );
            }
        }
    }

    /**
     * @return ResourceActionDefinition[]
     */
    public function getResourceActionDefinitions(): array
    {
        return $this->definitions;
    }
}
