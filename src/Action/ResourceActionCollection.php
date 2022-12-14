<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionCollectionException\ActionForResourceNotFound;
use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinitionCollection;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;

final class ResourceActionCollection
{
    /** @var array<string, ResourceAction> */
    private array $actions = [];

    /**
     * @param ResourceActionDefinitionCollection $resourceActionDefinitionCollection
     * @param ResourceMetadataFactoryInterface $resourceMetadataFactory
     * @throws CompileTimeExceptionInterface
     */
    public function __construct(
        ResourceActionDefinitionCollection $resourceActionDefinitionCollection,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ) {
        foreach ($resourceActionDefinitionCollection->getResourceActionDefinitions() as $resourceActionDefinition) {
            $action = new ResourceAction(
                $resourceActionDefinition->action,
                $resourceActionDefinition->resource,
                $resourceActionDefinition->command,
                $resourceActionDefinition->description,
                $resourceMetadataFactory
            );

            $this->actions[$action->getResource() . $action->getName()] = $action;
        }
    }

    /**
     * @param string $resourceClass
     * @param string $actionName
     * @return ResourceAction
     * @throws RuntimeExceptionInterface
     */
    public function getAction(string $resourceClass, string $actionName): ResourceAction
    {
        if (!array_key_exists($resourceClass . $actionName, $this->actions)) {
            throw ActionForResourceNotFound::create($resourceClass, $actionName);
        }

        return $this->actions[$resourceClass . $actionName];
    }

    /**
     * @return ResourceAction[]
     */
    public function getActions(): array
    {
        return array_values($this->actions);
    }

    /**
     * @param string $resource
     * @return ResourceAction[]
     */
    public function getActionsForResource(string $resource): array
    {
        return array_values(
            array_filter($this->actions, static function (ResourceAction $action) use ($resource): bool {
                return $resource === $action->getResource();
            })
        );
    }
}
