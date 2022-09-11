<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Request;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionCollection;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContext;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;

final class RequestTransformer implements DataTransformerInterface
{
    /**
     * @param ResourceActionCollection $actionCollection
     */
    public function __construct(private ResourceActionCollection $actionCollection)
    {
    }

    /**
     * @param mixed $data
     * @param string $to
     * @param array<string, mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if (!is_array($data)) {
            return false;
        }

        return Request::class === ($context['input']['class'] ?? null);
    }

    /**
     * There is no actual transformation in this method,
     * but it's the best way to add data to the payload and set the resource class of the operation.
     *
     * @param Request $object
     * @param string $to
     * @param array<string, mixed> $context
     * @return object
     * @throws RuntimeExceptionInterface
     */
    public function transform($object, string $to, array $context = []): object
    {
        $apiPlatformContext = new ApiPlatformContext($context);
        $object->apiPlatformContext = $apiPlatformContext;
        $object->resource = $apiPlatformContext->getResourceClass();

        $action = $this->actionCollection->getAction($apiPlatformContext->getResourceClass(), $object->action);
        $constructorParameterName = $action->getCommandMetadata()->getConstructorParameterNameForType(
            get_class($apiPlatformContext->getResourceObject()),
            array_keys($object->payload)
        );
        if (null === $constructorParameterName) {
            return $object;
        }

        $object->payload[$constructorParameterName] = $apiPlatformContext->getResourceObject();

        return $object;
    }
}
