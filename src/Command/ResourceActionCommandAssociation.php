<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Command;

use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandAssociationException\ActionBlankException;
use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandAssociationException\CommandBlankException;
use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandAssociationException\CommandNotAClassException;
use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandAssociationException\ResourceBlankException;
use ITB\ApiPlatformUpdateActionsBundle\Exception\CompileTimeExceptionInterface;

final class ResourceActionCommandAssociation
{
    /**
     * @param string $resource
     * @param string $action
     * @param string $commandClass
     * @throws CompileTimeExceptionInterface
     */
    public function __construct(private string $resource, private string $action, private string $commandClass)
    {
        if ('' === $this->resource) {
            throw ResourceBlankException::create();
        }

        if ('' === $this->action) {
            throw ActionBlankException::create();
        }

        if ('' === $this->commandClass) {
            throw CommandBlankException::create();
        }
        if (!class_exists($this->commandClass, true)) {
            throw CommandNotAClassException::create($this->commandClass);
        }
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getCommandClass(): string
    {
        return $this->commandClass;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }
}