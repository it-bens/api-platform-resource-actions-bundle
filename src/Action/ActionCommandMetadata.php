<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action;

use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\CommandConstructorParameterRetrievalFailedException;
use ITB\ApiPlatformResourceActionsBundle\Action\ActionException\CommandNotAClassException;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ReflectionConstructor\ReflectionConstructor;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

final class ActionCommandMetadata
{
    /** @var ReflectionClass $reflectionClass */
    /** @phpstan-ignore-next-line */
    private ReflectionClass $reflectionClass;
    /** @var ReflectionConstructor|null $reflectionConstructor */
    private ?ReflectionConstructor $reflectionConstructor;
    /** @var ReflectionParameter[] $reflectionConstructorParameters */
    private array $reflectionConstructorParameters;

    /**
     * @param string $commandClass
     * @throws CompileTimeExceptionInterface
     */
    public function __construct(private string $commandClass)
    {
        try {
            /** @phpstan-ignore-next-line */
            $this->reflectionClass = new ReflectionClass($this->commandClass);
        } catch (ReflectionException $exception) {
            throw CommandNotAClassException::create($this->commandClass);
        }

        try {
            $this->reflectionConstructor = new ReflectionConstructor($this->commandClass);
        } catch (ReflectionException $exception) {
            $this->reflectionConstructor = null;
        }

        $this->reflectionConstructorParameters = null !== $this->reflectionClass->getConstructor(
        ) ? $this->reflectionClass->getConstructor()->getParameters() : [];
    }

    /**
     * @param string $type
     * @param string[] $excludedParameters
     * @return string|null
     * @throws RuntimeExceptionInterface
     */
    public function getConstructorParameterNameForType(string $type, array $excludedParameters): ?string
    {
        if (null === $this->reflectionConstructor) {
            return null;
        }

        try {
            return $this->reflectionConstructor->extractParameterNameForClassName($type, $excludedParameters);
        } catch (ReflectionException $exception) {
            throw CommandConstructorParameterRetrievalFailedException::create($this->commandClass, $type, $exception);
        }
    }

    /**
     * @return ReflectionParameter[]
     */
    public function getConstructorParameters(): array
    {
        return $this->reflectionConstructorParameters;
    }
}
