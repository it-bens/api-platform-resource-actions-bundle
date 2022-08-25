<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Context;

use ApiPlatform\Core\Bridge\Symfony\Messenger\ContextStamp;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException\ObjectToPopulateMissingException;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException\ObjectToPopulateNotAnObjectException;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException\ResourceClassMissingException;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException\ResourceClassNotAStringException;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class ApiPlatformContext
{
    private const RESOURCE_OBJECT_KEY = AbstractNormalizer::OBJECT_TO_POPULATE;

    /** @var string $resourceClass */
    private string $resourceClass;
    /** @var object $resourceObject */
    private object $resourceObject;

    /**
     * @param array<string, mixed> $context
     * @throws RuntimeExceptionInterface
     */
    public function __construct(private array $context)
    {
        if (!array_key_exists('resource_class', $this->context)) {
            throw ResourceClassMissingException::create();
        }
        if (!is_string($this->context['resource_class'])) {
            throw ResourceClassNotAStringException::create();
        }
        $this->resourceClass = $this->context['resource_class'];

        if (!array_key_exists(self::RESOURCE_OBJECT_KEY, $this->context)) {
            throw ObjectToPopulateMissingException::create();
        }
        if (!is_object($this->context[self::RESOURCE_OBJECT_KEY])) {
            throw ObjectToPopulateNotAnObjectException::create();
        }
        $this->resourceObject = $this->context[self::RESOURCE_OBJECT_KEY];
    }

    /**
     * @return string
     */
    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }

    /**
     * @return object
     */
    public function getResourceObject(): object
    {
        return $this->resourceObject;
    }

    /**
     * @return ContextStamp
     */
    public function toContextStamp(): ContextStamp
    {
        return new ContextStamp($this->context);
    }
}
