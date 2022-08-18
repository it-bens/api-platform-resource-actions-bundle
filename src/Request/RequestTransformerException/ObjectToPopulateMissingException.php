<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformerException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Throwable;

final class ObjectToPopulateMissingException extends Exception implements RuntimeExceptionInterface
{
    /**
     * @return ObjectToPopulateMissingException
     */
    public static function create(): ObjectToPopulateMissingException
    {
        return new self(
            sprintf(
                'The \'%s\' key is missing from the Api Platform deserialization context.',
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