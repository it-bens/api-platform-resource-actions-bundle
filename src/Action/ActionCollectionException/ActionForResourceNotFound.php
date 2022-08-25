<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Action\ActionCollectionException;

use Exception;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Throwable;

final class ActionForResourceNotFound extends Exception implements RuntimeExceptionInterface
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
     * @return ActionForResourceNotFound
     */
    public static function create(string $resource, string $action): ActionForResourceNotFound
    {
        return new self(
            sprintf(
                'There is no associated action for the API Platform resource "%s" and the action name "%s".',
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
