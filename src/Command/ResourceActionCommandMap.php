<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Command;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandMapException\CommandForResourceActionNotFound;
use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandMapException\ResourceNotRegisteredException;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceActionCommandMap
{
    /** @var array<string, ResourceActionCommandAssociation> */
    private array $associations = [];

    /**
     * @param array<int, array{'resource': string, 'action': string, 'commandClass': class-string}> $associations
     * @throws CompileTimeExceptionInterface
     */
    public function __construct(array $associations, ResourceMetadataFactoryInterface $resourceMetadataFactory)
    {
        foreach ($associations as $associationData) {
            $association = new ResourceActionCommandAssociation(
                $associationData['resource'],
                $associationData['action'],
                $associationData['commandClass']
            );

            try {
                $resourceMetadataFactory->create($association->getResource());
            } catch (ResourceClassNotFoundException $exception) {
                throw ResourceNotRegisteredException::create($association->getResource(), $exception);
            }
            $this->associations[$association->getResource() . $association->getAction()] = $association;
        }
    }

    /**
     * @param string $resource
     * @return string[]
     */
    public function getActionsForResource(string $resource): array
    {
        $actions = [];
        foreach ($this->associations as $association) {
            if ($resource !== $association->getResource()) {
                continue;
            }

            $actions[] = $association->getAction();
        }

        return array_unique($actions);
    }

    /**
     * @return ResourceActionCommandAssociation[]
     */
    public function getAssociations(): array
    {
        return array_values($this->associations);
    }

    /**
     * @param string $resourceClass
     * @param string $actionName
     * @return string
     * @throws CommandForResourceActionNotFound
     */
    public function getCommandClassForResourceAction(string $resourceClass, string $actionName): string
    {
        if (!array_key_exists($resourceClass . $actionName, $this->associations)) {
            throw CommandForResourceActionNotFound::create($resourceClass, $actionName);
        }

        return $this->associations[$resourceClass . $actionName]->getCommandClass();
    }

    /**
     * @return string[]
     */
    public function getResources(): array
    {
        return array_unique(
            array_map(static function (ResourceActionCommandAssociation $association): string {
                return $association->getResource();
            }, $this->associations)
        );
    }
}