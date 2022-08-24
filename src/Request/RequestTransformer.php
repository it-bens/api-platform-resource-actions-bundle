<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Request;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ITB\ApiPlatformUpdateActionsBundle\Action\ActionCollection;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ObjectToPopulateMissingException;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ObjectToPopulateNotAnObjectException;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ResourceClassMissingException;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ResourceClassNotAStringException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class RequestTransformer implements DataTransformerInterface
{
    /**
     * @param ActionCollection $actionCollection
     */
    public function __construct(private ActionCollection $actionCollection)
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
        if (!array_key_exists('resource_class', $context)) {
            throw ResourceClassMissingException::create();
        }
        if (!is_string($context['resource_class'])) {
            throw ResourceClassNotAStringException::create();
        }
        $object->resource = $context['resource_class'];

        if (!array_key_exists(AbstractNormalizer::OBJECT_TO_POPULATE, $context)) {
            throw ObjectToPopulateMissingException::create();
        }
        if (!is_object($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {
            throw ObjectToPopulateNotAnObjectException::create();
        }

        $action = $this->actionCollection->getAction($object->resource, $object->action);
        $constructorParameterName = $action->getCommandMetadata()->getConstructorParameterNameForType(
            get_class($context[AbstractNormalizer::OBJECT_TO_POPULATE]),
            array_keys($object->payload)
        );
        if (null === $constructorParameterName) {
            return $object;
        }

        $object->payload[$constructorParameterName] = $context[AbstractNormalizer::OBJECT_TO_POPULATE];

        return $object;
    }
}