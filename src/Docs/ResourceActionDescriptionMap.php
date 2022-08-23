<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs;

use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandMap;
use ITB\ApiPlatformUpdateActionsBundle\Docs\ResourceActionDescriptionMapException\DescriptionForResourceActionNotFound;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceActionDescriptionMap
{
    /** @var array<string, ResourceActionDescriptionAssociation> */
    private array $associations = [];

    /**
     * @param array<string, array<string, string>> $descriptions
     * @throws CompileTimeExceptionInterface
     */
    public function __construct(private ResourceActionCommandMap $resourceActionCommandMap, array $descriptions)
    {
        foreach ($this->resourceActionCommandMap->getAssociations() as $commandAssociation) {
            $association = new ResourceActionDescriptionAssociation(
                $commandAssociation,
                $descriptions[$commandAssociation->getResource()][$commandAssociation->getAction()]
            );

            $this->associations[$association->getResource() . $association->getAction()] = $association;
        }
    }

    /**
     * @param string $resourceClass
     * @param string $actionName
     * @return string|null
     * @throws CompileTimeExceptionInterface
     */
    public function getDescriptionForResourceAction(string $resourceClass, string $actionName): ?string
    {
        if (!array_key_exists($resourceClass . $actionName, $this->associations)) {
            throw DescriptionForResourceActionNotFound::create($resourceClass, $actionName);
        }

        return $this->associations[$resourceClass . $actionName]->getDescription();
    }
}