<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Request;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandMap;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ConstructionArgumentNameExtractionFailed;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ObjectToPopulateMissingException;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ObjectToPopulateNotAnObjectException;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ResourceClassMissingException;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException\ResourceClassNotAStringException;
use ITB\ReflectionConstructor\ReflectionConstructor;
use ReflectionException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class RequestTransformer implements DataTransformerInterface
{
    /**
     * @param ResourceActionCommandMap $resourceActionCommandMap
     */
    public function __construct(private ResourceActionCommandMap $resourceActionCommandMap)
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
     * @param $object
     * @param string $to
     * @param array $context
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
        $commandClass = $this->resourceActionCommandMap->getCommandClass($object->resource, $object->action);

        if (!array_key_exists(AbstractNormalizer::OBJECT_TO_POPULATE, $context)) {
            throw ObjectToPopulateMissingException::create();
        }
        if (!is_object($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {
            throw ObjectToPopulateNotAnObjectException::create();
        }
        try {
            $reflectionConstructor = new ReflectionConstructor($commandClass);
            $constructorParameterName = $reflectionConstructor->extractParameterNameForObject(
                $context[AbstractNormalizer::OBJECT_TO_POPULATE]
            );
        } catch (ReflectionException $exception) {
            throw ConstructionArgumentNameExtractionFailed::create(
                $commandClass,
                get_class($context[AbstractNormalizer::OBJECT_TO_POPULATE]),
                $exception
            );
        }

        $object->payload[$constructorParameterName] = $context[AbstractNormalizer::OBJECT_TO_POPULATE];

        return $object;
    }
}