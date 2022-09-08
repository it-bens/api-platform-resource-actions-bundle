<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Definition;

use ITB\ApiPlatformResourceActionsBundle\Definition\Collector\AttributeCollector;
use ITB\ApiPlatformResourceActionsBundle\Definition\Collector\CollectorInterface;
use ITB\ApiPlatformResourceActionsBundle\Definition\Collector\ConfigurationCollector;
use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinitionCollectionException\ResourceActionNotUniqueException;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceActionDefinitionCollection
{
    /** @var CollectorInterface[] */
    private array $collectors = [];

    /**
     * @param array<string, array<string, array{
     *     command_class: class-string,
     *     description: string|null
     * }>> $resourcesConfiguration
     */
    public function __construct(array $resourcesConfiguration)
    {
        $this->collectors[] = new ConfigurationCollector($resourcesConfiguration);
        $this->collectors[] = new AttributeCollector();
    }

    /**
     * @return ResourceActionDefinition[]
     * @throws CompileTimeExceptionInterface
     */
    public function getResourceActionDefinitions(): array
    {
        $definitions = [];
        // The map is used to check if an action already exists.
        $resourceActionMap = [];

        foreach ($this->collectors as $collector) {
            foreach ($collector->getResourceActionDefinitions() as $resourceActionDefinition) {
                // Check if the action for the resource is already in the map. The combination has to be unique.
                if (
                    array_key_exists($resourceActionDefinition->resource, $resourceActionMap) && in_array(
                        $resourceActionDefinition->action,
                        $resourceActionMap[$resourceActionDefinition->resource]
                    )
                ) {
                    throw ResourceActionNotUniqueException::create(
                        $resourceActionDefinition->resource,
                        $resourceActionDefinition->action
                    );
                }

                $definitions[] = $resourceActionDefinition;
                // The definition's resource (as map key) and action (as array element) are added to the map to allow checking for uniqueness.
                $resourceActionMap[$resourceActionDefinition->resource] = array_merge(
                    $resourceActionMap[$resourceActionDefinition->resource] ?? [],
                    [$resourceActionDefinition->action]
                );
            }
        }

        return $definitions;
    }
}
