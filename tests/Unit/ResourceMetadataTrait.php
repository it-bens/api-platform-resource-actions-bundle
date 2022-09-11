<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Unit;

use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ITB\ApiPlatformResourceActionsBundle\Controller\Controller;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use ReflectionClass;
use ReflectionException;

trait ResourceMetadataTrait
{
    /**
     * @param string $resourceClass
     * @return ResourceMetadata
     * @throws ReflectionException
     */
    private function createResourceMetadataOperationWithInvalidController(string $resourceClass): ResourceMetadata
    {
        /** @phpstan-ignore-next-line */
        $shortName = (new ReflectionClass($resourceClass))->getShortName();

        return new ResourceMetadata($shortName, itemOperations: [
            'some operation' => [
                'input' => ['class' => Request::class],
                'controller' => 'I am not a controller!'
            ]
        ]);
    }

    /**
     * @param string $resourceClass
     * @return ResourceMetadata
     * @throws ReflectionException
     */
    private function createResourceMetadataOperationWithInvalidInput(string $resourceClass): ResourceMetadata
    {
        /** @phpstan-ignore-next-line */
        $shortName = (new ReflectionClass($resourceClass))->getShortName();

        return new ResourceMetadata($shortName, itemOperations: [
            'some operation' => [
                'input' => 'What the hell am I doing here?',
                'controller' => Controller::class
            ]
        ]);
    }

    /**
     * @param string $resourceClass
     * @param string $operationName
     * @return ResourceMetadata
     * @throws ReflectionException
     */
    private function createResourceMetadataWithValidOperation(string $resourceClass, string $operationName): ResourceMetadata
    {
        /** @phpstan-ignore-next-line */
        $shortName = (new ReflectionClass($resourceClass))->getShortName();

        return new ResourceMetadata($shortName, itemOperations: [
            $operationName => [
                'input' => ['class' => Request::class],
                'controller' => Controller::class
            ]
        ]);
    }

    /**
     * @param string $operationName
     * @return ResourceMetadata
     */
    private function createResourceMetadataWithoutShortName(string $operationName): ResourceMetadata
    {
        return new ResourceMetadata(itemOperations: [
            $operationName => [
                'input' => ['class' => Request::class],
                'controller' => Controller::class
            ]
        ]);
    }
}
