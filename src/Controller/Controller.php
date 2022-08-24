<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException as ApiPlatformValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface as ApiPlatformValidatorInterface;
use ITB\ApiPlatformUpdateActionsBundle\Command\CommandFactory;
use ITB\ApiPlatformUpdateActionsBundle\Controller\ControllerException\RequestApiPlatformContextIsNullException;
use ITB\ApiPlatformUpdateActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformUpdateActionsBundle\Request\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final class Controller
{
    use HandleTrait;

    /**
     * @param CommandFactory $commandFactory
     * @param ApiPlatformValidatorInterface $validator
     * @param MessageBusInterface $defaultBus
     * @param bool $validateCommand
     * @param bool $ignoreMessengerValidation
     */
    public function __construct(
        private CommandFactory $commandFactory,
        private ApiPlatformValidatorInterface $validator,
        private MessageBusInterface $defaultBus,
        private bool $validateCommand,
        private bool $ignoreMessengerValidation
    ) {
        $this->messageBus = $this->defaultBus;
    }

    /**
     * Creates the command object from the Request.
     * Explicit validation is performed via ApiPlatformValidatorInterface if configured.
     * Exceptions from implicit validation via ValidationMiddleware are wrapped with Api Platform validation exceptions.
     * Any bundle-specific exceptions will be wrapped with an exception expected by Api Platform.
     *
     * @param Request $data
     * @return object
     * @throws Throwable (Api Platform compatible)
     * @throws ApiPlatformValidationException
     */
    public function __invoke(Request $data): object
    {
        if (null === $data->apiPlatformContext) {
            throw RequestApiPlatformContextIsNullException::create($data);
        }

        // The exceptions thrown by this bundle will be wrapped with exceptions Api Platform will expect.
        try {
            $command = $this->commandFactory->createCommand($data);
        } catch (RuntimeExceptionInterface $exception) {
            throw $exception->createApiPlatformCompatibleException();
        }

        // The exception thrown by the ApiPlatformValidatorInterface will be expected by Api Platform.
        if (true === $this->validateCommand) {
            $this->validator->validate($command);
        }

        // The exception thrown by the ValidationMiddleware will be wrapped with the ApiPlatformValidationException,
        // which is expected by Api Platform.
        try {
            return $this->handle(Envelope::wrap($command, [$data->apiPlatformContext->toContextStamp()]));
        } catch (ValidationFailedException $exception) {
            if (false === $this->ignoreMessengerValidation) {
                throw new ApiPlatformValidationException($exception->getViolations(), previous: $exception);
            }

            throw $exception;
        }
    }
}
