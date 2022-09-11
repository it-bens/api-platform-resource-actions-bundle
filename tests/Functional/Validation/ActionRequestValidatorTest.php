<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Validation;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use ITB\ApiPlatformResourceActionsBundle\Validation\ActionRequest;
use ITB\ApiPlatformResourceActionsBundle\Validation\ActionRequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildRequestTrait;

final class ActionRequestValidatorTest extends TestCase
{
    use BuildAndBootKernelTrait;
    use BuildRequestTrait;

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForInvalidRequestIncompletePayload(): Generator
    {
        $request = $this->buildInitializedRequestToDoNothing();
        $request->payload = [];
        yield [$request, $this->getValidator()];
    }

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForInvalidRequestUnknownAction(): Generator
    {
        $request = $this->buildInitializedRequestToDoNothing();
        $request->action = 'this-action-is-not-registered';
        yield [$request, $this->getValidator()];
    }

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForInvalidUnexpectedType(): Generator
    {
        $validator = $this->getActionRequestValidator();
        $request = $this->buildInitializedRequestToDoNothing();
        $constraint = $this->createMock(Constraint::class);

        yield [$validator, $request, $constraint];
    }

    /**
     * @return Generator
     */
    public function provideForInvalidUnexpectedValue(): Generator
    {
        $validator = $this->getActionRequestValidator();
        $request = 'Not even an object';
        $constraint = new ActionRequest();

        yield [$validator, $request, $constraint];
    }

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForValidRequest(): Generator
    {
        yield [$this->buildInitializedRequestToDoNothing(), $this->getValidator()];
    }

    /**
     * @dataProvider provideForInvalidRequestIncompletePayload
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return void
     */
    public function testInvalidRequestIncompletePayload(Request $request, ValidatorInterface $validator): void
    {
        $constraintViolations = $validator->validate($request);
        $this->assertCount(1, $constraintViolations);

        /** @var ConstraintViolationInterface $violation */
        $violation = $constraintViolations[0];
        $this->assertEquals('payload', $violation->getPropertyPath());
    }

    /**
     * @dataProvider provideForInvalidRequestUnknownAction
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return void
     */
    public function testInvalidRequestUnknownAction(Request $request, ValidatorInterface $validator): void
    {
        $constraintViolations = $validator->validate($request);
        $this->assertCount(1, $constraintViolations);

        /** @var ConstraintViolationInterface $violation */
        $violation = $constraintViolations[0];
        $this->assertEquals('action', $violation->getPropertyPath());
    }

    /**
     * @dataProvider provideForInvalidUnexpectedType
     *
     * @param ActionRequestValidator $requestValidator
     * @param Request $request
     * @param Constraint $constraint
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function testInvalidUnexpectedType(ActionRequestValidator $requestValidator, Request $request, Constraint $constraint): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $requestValidator->validate($request, $constraint);
    }

    /**
     * @dataProvider provideForInvalidUnexpectedValue
     *
     * @param ActionRequestValidator $requestValidator
     * @param Request $request
     * @param Constraint $constraint
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function testInvalidUnexpectedValue(ActionRequestValidator $requestValidator, mixed $request, Constraint $constraint): void
    {
        $this->expectException(UnexpectedValueException::class);
        $requestValidator->validate($request, $constraint);
    }

    /**
     * @dataProvider provideForValidRequest
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return void
     */
    public function testValidRequest(Request $request, ValidatorInterface $validator): void
    {
        $constraintViolations = $validator->validate($request);
        $this->assertCount(0, $constraintViolations);
    }

    /**
     * @return ActionRequestValidator
     */
    private function getActionRequestValidator(): ActionRequestValidator
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );
        /** @var ActionRequestValidator $validator */
        $validator = $kernel->getContainer()->get(ActionRequestValidator::class);

        return $validator;
    }

    /**
     * @return ValidatorInterface
     */
    private function getValidator(): ValidatorInterface
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );
        /** @var ValidatorInterface $validator */
        $validator = $kernel->getContainer()->get('validator');

        return $validator;
    }
}
