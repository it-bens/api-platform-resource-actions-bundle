<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Definition\Collector;

use ITB\ApiPlatformResourceActionsBundle\Attribute\ResourceAction;
use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinition;
use ReflectionClass;

final class AttributeCollector implements CollectorInterface
{
    /** @var ResourceActionDefinition[] $definitions */
    private array $definitions = [];

    public function __construct()
    {
        foreach (get_declared_classes() as $class) {
            $reflection = new ReflectionClass($class);
            foreach ($reflection->getAttributes(ResourceAction::class) as $reflectionAttribute) {
                /** @var ResourceAction $resourceAction */
                $resourceAction = $reflectionAttribute->newInstance();
                $this->definitions[] = ResourceActionDefinition::fromResourceActionAttribute($resourceAction, $class);
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
