<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Action;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ITB\ApiPlatformUpdateActionsBundle\Action\ActionCollectionException\ActionForResourceNotFound;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;

final class ActionCollection
{
    /** @var array<string, Action> */
    private array $actions = [];

    /**
     * @param array<int, array{
     *     'resource': string,
     *     'action': string,
     *     'commandClass': class-string,
     *     'description': string|null
     * }> $actionsData
     * @throws CompileTimeExceptionInterface
     */
    public function __construct(array $actionsData, ResourceMetadataFactoryInterface $resourceMetadataFactory)
    {
        foreach ($actionsData as $actionData) {
            $action = new Action(
                $actionData['action'],
                $actionData['resource'],
                $actionData['commandClass'],
                $actionData['description'],
                $resourceMetadataFactory
            );

            $this->actions[$action->getResource() . $action->getName()] = $action;
        }
    }

    /**
     * @param string $resourceClass
     * @param string $actionName
     * @return Action
     * @throws RuntimeExceptionInterface
     */
    public function getAction(string $resourceClass, string $actionName): Action
    {
        if (!array_key_exists($resourceClass . $actionName, $this->actions)) {
            throw ActionForResourceNotFound::create($resourceClass, $actionName);
        }

        return $this->actions[$resourceClass . $actionName];
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return array_values($this->actions);
    }

    /**
     * @param string $resource
     * @return Action[]
     */
    public function getActionsForResource(string $resource): array
    {
        return array_values(
            array_filter($this->actions, static function (Action $action) use ($resource): bool {
                return $resource === $action->getResource();
            })
        );
    }
}
