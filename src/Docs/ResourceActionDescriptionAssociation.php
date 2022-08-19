<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Docs;

use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandAssociation;
use ITB\ApiPlatformUpdateActionsBundle\Docs\ResourceActionDescriptionAssociationException\DescriptionBlankException;

final class ResourceActionDescriptionAssociation
{
    /**
     * @param ResourceActionCommandAssociation $resourceActionCommandAssociation
     * @param string|null $description
     * @throws DescriptionBlankException
     */
    public function __construct(
        private ResourceActionCommandAssociation $resourceActionCommandAssociation,
        private ?string $description
    ) {
        if ('' === $this->description) {
            throw DescriptionBlankException::create();
        }
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->resourceActionCommandAssociation->getAction();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resourceActionCommandAssociation->getResource();
    }
}