<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Command;

use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

interface CommandFactoryInterface
{
    /**
     * Creates the command object from the request object with 'resource', 'action' and 'payload'.
     * The denormalizer exceptions are not caught to pass the proper handling to API Platform.
     *
     * @param Request $request
     * @return object
     * @throws RuntimeExceptionInterface
     * @throws ExceptionInterface (denormalization exception)
     */
    public function createCommand(Request $request): object;
}
