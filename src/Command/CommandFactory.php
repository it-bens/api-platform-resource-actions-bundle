<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Command;

use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionCollection;
use ITB\ApiPlatformResourceActionsBundle\Command\CommandFactoryException\RequestResourceIsNullException;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandFactory implements CommandFactoryInterface
{
    /**
     * @param ResourceActionCollection $actionCollection
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(
        private ResourceActionCollection $actionCollection,
        private DenormalizerInterface $denormalizer
    ) {
    }

    /**
     * @param Request $request
     * @return object
     * @throws ExceptionInterface
     * @throws RequestResourceIsNullException
     * @throws RuntimeExceptionInterface
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
