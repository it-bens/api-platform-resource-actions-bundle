<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Validation;

use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionCollectionException\ActionForResourceNotFound;
use ITB\ApiPlatformResourceActionsBundle\Command\CommandFactory;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ActionRequestValidator extends ConstraintValidator
{
    /**
     * @param CommandFactory $commandFactory
     */
    public function __construct(private CommandFactory $commandFactory)
    {
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ActionRequest) {
            throw new UnexpectedTypeException($constraint, ActionRequest::class);
        }

        if (!$value instanceof Request) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, Request::class);
        }

        try {
            $this->commandFactory->createCommand($value);
        } catch (ActionForResourceNotFound $exception) {
            // The resource is checked for null value in the createCommand method.
            /** @var string $resource */
            $resource = $value->resource;

            $this->context->buildViolation($constraint->actionUnknownMessage)
                ->setParameter('{{ action }}', $value->action)
                ->setParameter('{{ resource }}', $resource)
                ->atPath('action')
                ->addViolation();
        } catch (ExceptionInterface $exception) {
            $this->context->buildViolation($constraint->denormalizationFailedMessage)
                ->atPath('payload')
                ->addViolation();
        }
    }
}
