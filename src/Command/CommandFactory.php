<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Command;

use ITB\ApiPlatformResourceActionsBundle\Action\ActionCollection;
use ITB\ApiPlatformResourceActionsBundle\Command\CommandFactoryException\RequestResourceIsNullException;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandFactory
{
    /**
     * @param ActionCollection $actionCollection
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(
        private ActionCollection $actionCollection,
        private DenormalizerInterface $denormalizer
    ) {
    }

    /**
     * Creates the command object from the request object with 'resource', 'action' and 'payload'.
     * The denormalizer exceptions are not caught to pass the proper handling to API Platform.
     *
     * @param Request $request
     * @return object
     * @throws RuntimeExceptionInterface
     * @throws ExceptionInterface (denormalization exception)
     */
    public function createCommand(Request $request): object
    {
        if (null === $request->resource) {
            throw RequestResourceIsNullException::create($request);
        }

        $action = $this->actionCollection->getAction($request->resource, $request->action);

        return $this->denormalizer->denormalize($request->payload, $action->getCommandClass());
    }
}
