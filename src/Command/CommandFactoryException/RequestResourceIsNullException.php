<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Command\CommandFactoryException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformUpdateActionsBundle\Request\RequestTransformer;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Throwable;

final class RequestResourceIsNullException extends Exception implements RuntimeExceptionInterface
{
    /**
     * @param string $message
     * @param mixed $data
     */
    public function __construct(string $message, private mixed $data)
    {
        parent::__construct($message, 0, null);
    }

    /**
     * @param mixed $data
     * @return RequestResourceIsNullException
     */
    public static function create(mixed $data): RequestResourceIsNullException
    {
        return new self(
            sprintf(
                'The resource property of the request is null. It should contain the API Platform resource the action will be applied on. The property should be set in the %s class.',
                RequestTransformer::class
            ),
            $data
        );
    }

    /**
     * @return Throwable
     */
    public function createApiPlatformCompatibleException(): Throwable
    {
        return NotNormalizableValueException::createForUnexpectedDataType(
            $this->message,
            $this->data,
            [],
            useMessageForUser: false,
            previous: $this
        );
    }
}