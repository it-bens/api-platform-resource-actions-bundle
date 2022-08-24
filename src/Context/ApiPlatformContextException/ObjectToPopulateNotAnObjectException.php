<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Context\ApiPlatformContextException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Throwable;

final class ObjectToPopulateNotAnObjectException extends Exception implements RuntimeExceptionInterface
{
    /**
     * @return ObjectToPopulateNotAnObjectException
     */
    public static function create(): ObjectToPopulateNotAnObjectException
    {
        return new self(
            sprintf(
                'The \'%s\' from the Api Platform deserialization context is not an object.',
                AbstractNormalizer::OBJECT_TO_POPULATE
            )
        );
    }

    /**
     * @return Throwable
     */
    public function createApiPlatformCompatibleException(): Throwable
    {
        // TODO: Implement createApiPlatformCompatibleException() method.
    }
}
