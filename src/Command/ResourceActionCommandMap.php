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
    private array $entries = [];

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
            $this->entries[$association->getResource() . $association->getAction()] = $association;
        }
    }

    /**
     * @param string $resourceClass
     * @param string $actionName
     * @return string
     * @throws CommandForResourceActionNotFound
     */
    public function getCommandClass(string $resourceClass, string $actionName): string
    {
        if (!array_key_exists($resourceClass . $actionName, $this->entries)) {
            throw CommandForResourceActionNotFound::create($resourceClass, $actionName);
        }

        return $this->entries[$resourceClass . $actionName]->getCommandClass();
    }
}