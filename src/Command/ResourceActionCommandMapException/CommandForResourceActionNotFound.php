<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Command\ResourceActionCommandMapException;

use Exception;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Throwable;

final class CommandForResourceActionNotFound extends Exception implements RuntimeExceptionInterface
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
     * @param string $resource
     * @param string $action
     * @return CommandForResourceActionNotFound
     */
    public static function create(string $resource, string $action): CommandForResourceActionNotFound
    {
        return new self(
            sprintf(
                'There is no associated command for the API Platform resource "%s" and the action "%s".',
                $resource,
                $action
            ),
            implode(['resource: ' . $resource, 'action: ' . $action])
        );
    }

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