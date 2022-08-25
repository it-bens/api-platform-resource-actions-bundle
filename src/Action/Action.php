<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\CommandBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\CommandNotAClassException;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\DescriptionBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\NameBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\NoOperationConfiguredForActionException;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\ResourceBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\ResourceHasNoShortNameException;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\ResourceNotRegisteredException;
use ITB\ApiPlatformResourceActionsBundle\Controller\Controller;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;

final class Action
{
    /** @var ActionCommandMetadata $commandMetadata */
    private ActionCommandMetadata $commandMetadata;

    /** @var string $resourceName */
    private string $resourceName;
    /** @var string $operationName */
    private string $operationName;
    /** @var array<string, mixed> $operationData */
    private array $operationData;

    /**
     * @param string $name
     * @param string $resource
     * @param string $commandClass
     * @param string|null $description
     * @param ResourceMetadataFactoryInterface $resourceMetadataFactory
     * @throws CompileTimeExceptionInterface
     */
    public function __construct(
        private string $name,
        private string $resource,
        private string $commandClass,
        private ?string $description,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ) {
        if ('' === $this->name) {
            throw NameBlankException::create();
        }

        if ('' === $this->resource) {
            throw ResourceBlankException::create();
        }

        if ('' === $this->commandClass) {
            throw CommandBlankException::create();
        }
        if (!class_exists($this->commandClass)) {
            throw CommandNotAClassException::create($this->commandClass);
        }

        $this->commandMetadata = new ActionCommandMetadata($this->commandClass);

        if ('' === $this->description) {
            throw DescriptionBlankException::create();
        }

        try {
            $resourceMetadata = $resourceMetadataFactory->create($this->resource);
        } catch (ResourceClassNotFoundException $exception) {
            throw ResourceNotRegisteredException::create($this->resource, $exception);
        }

        if (null === $resourceMetadata->getShortName()) {
            throw ResourceHasNoShortNameException::create($this->resource);
        }
        $this->resourceName = $resourceMetadata->getShortName();

        // Only the first matching operation will be used.
        // The usage of multiple operations for actions is currently not supported.
        foreach ($resourceMetadata->getItemOperations() ?? [] as $operationName => $operationData) {
            if (
                !array_key_exists('input', $operationData)
                || !is_array($operationData['input'])
                || !array_key_exists('class', $operationData['input'])
                || Request::class !== $operationData['input']['class']
            ) {
                continue;
            }

            if (
                !array_key_exists('controller', $operationData)
                || Controller::class !== $operationData['controller']
            ) {
                continue;
            }

            $this->operationName = $operationName;
            $this->operationData = $operationData;
            break;
        }

        // 'isset' can be used to check if the class property is uninitialized, because it's not typed nullable.
        // 'isset' does not discriminate between, an uninitialized or a null value.
        // If 'operationName' was nullable, it could not bet used.
        if (!isset($this->operationName)) {
            throw NoOperationConfiguredForActionException::create($this->name, $this->resource);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getCommandClass(): string
    {
        return $this->commandClass;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return ActionCommandMetadata
     */
    public function getCommandMetadata(): ActionCommandMetadata
    {
        return $this->commandMetadata;
    }

    /**
     * @return string
     */
    public function getOperationName(): string
    {
        return $this->operationName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOperationData(): array
    {
        return $this->operationData;
    }

    /**
     * @return string
     */
    public function getResourceName(): string
    {
        return $this->resourceName;
    }
}
