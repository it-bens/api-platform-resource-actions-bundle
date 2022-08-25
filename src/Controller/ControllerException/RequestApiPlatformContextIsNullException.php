<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Controller\ControllerException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\RequestTransformer;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Throwable;

final class RequestApiPlatformContextIsNullException extends Exception implements RuntimeExceptionInterface
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
     * @return RequestApiPlatformContextIsNullException
     */
    public static function create(mixed $data): RequestApiPlatformContextIsNullException
    {
        return new self(
            sprintf(
                'The api platform context property of the request is null. The property should be set in the %s class.',
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
