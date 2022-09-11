<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException as ApiPlatformValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface as ApiPlatformValidatorInterface;
use ITB\ApiPlatformResourceActionsBundle\Command\CommandFactoryInterface;
use ITB\ApiPlatformResourceActionsBundle\Controller\ControllerException\RequestApiPlatformContextIsNullException;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use Throwable;

final class Controller
{
    /**
     * @param CommandFactoryInterface $commandFactory
     * @param ApiPlatformValidatorInterface $validator
     * @param bool $validateCommand
     */
    public function __construct(
        private CommandFactoryInterface $commandFactory,
        private ApiPlatformValidatorInterface $validator,
        private bool $validateCommand
    ) {
    }

    /**
     * Creates the command object from the Request and returns it to the write-listener (persistence is done there).
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

        // The command is handled by the default API Platform request flow. If 'write' is enabled, DataPersisters are called.
        return $command;
    }
}
