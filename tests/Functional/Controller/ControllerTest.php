<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface as ApiPlatformValidatorInterface;
use Generator;
use ITB\ApiPlatformResourceActionsBundle\Command\CommandFactoryException\RequestResourceIsNullException;
use ITB\ApiPlatformResourceActionsBundle\Command\CommandFactoryInterface;
use ITB\ApiPlatformResourceActionsBundle\Controller\Controller;
use ITB\ApiPlatformResourceActionsBundle\Controller\ControllerException\RequestApiPlatformContextIsNullException;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildRequestTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;
use Throwable;

final class ControllerTest extends TestCase
{
    use BuildAndBootKernelTrait;
    use BuildRequestTrait;

    private const CONTROLLER_ID = 'itb_api_platform_resource_actions.controller';

    /**
     * @return Generator
     */
    public function provideForInitialization(): Generator
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );

        yield [$kernel];
    }

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForInvalidCommandCreationFails(): Generator
    {
        $commandFactory = $this->createMock(CommandFactoryInterface::class);
        $commandFactory->method('createCommand')->willThrowException(RequestResourceIsNullException::create([]));
        $controller = new Controller($commandFactory, $this->createMock(ApiPlatformValidatorInterface::class), true);
        $request = $this->buildInitializedRequestToDoNothing();

        yield [$controller, $request];
    }

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForInvalidNoApiPlatformContext(): Generator
    {
        $controller = new Controller(
            $this->createMock(CommandFactoryInterface::class),
            $this->createMock(ApiPlatformValidatorInterface::class),
            true
        );
        $request = $this->buildInitializedRequestToDoNothing();
        $request->apiPlatformContext = null;

        yield [$controller, $request];
    }

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForValidWithValidation(): Generator
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories_and_validation.yaml',
            'api_platform_config.yaml'
        );
        $controller = $kernel->getContainer()->get(self::CONTROLLER_ID);

        yield [$controller, $this->buildInitializedRequestToDoNothing()];
    }

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForValidWithoutValidation(): Generator
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );
        $controller = $kernel->getContainer()->get(self::CONTROLLER_ID);

        yield [$controller, $this->buildInitializedRequestToDoNothing()];
    }

    /**
     * @dataProvider provideForInitialization
     *
     * @param KernelInterface $kernel
     * @return void
     */
    public function testInitialization(KernelInterface $kernel): void
    {
        $controller = $kernel->getContainer()->get(self::CONTROLLER_ID);
        $this->assertInstanceOf(Controller::class, $controller);
    }

    /**
     * @dataProvider provideForInvalidCommandCreationFails
     *
     * @param Controller $controller
     * @param Request $request
     * @return void
     * @throws Throwable
     */
    public function testInvalidCommandCreationFails(Controller $controller, Request $request): void
    {
        $this->expectException(NotNormalizableValueException::class);
        $controller($request);
    }

    /**
     * @dataProvider provideForInvalidNoApiPlatformContext
     *
     * @param Controller $controller
     * @param Request $request
     * @return void
     * @throws Throwable
     */
    public function testInvalidNoApiPlatformContext(Controller $controller, Request $request): void
    {
        $this->expectException(RequestApiPlatformContextIsNullException::class);
        $controller($request);
    }

    /**
     * @dataProvider provideForValidWithValidation
     *
     * @param Controller $controller
     * @param Request $request
     * @return void
     * @throws Throwable
     */
    public function testValidWithValidation(Controller $controller, Request $request): void
    {
        $result = $controller($request);
        $this->assertInstanceOf(DoNothingWithTheDocument::class, $result);
    }

    /**
     * @dataProvider provideForValidWithoutValidation
     *
     * @param Controller $controller
     * @param Request $request
     * @return void
     * @throws Throwable
     */
    public function testValidWithoutValidation(Controller $controller, Request $request): void
    {
        $result = $controller($request);
        $this->assertInstanceOf(DoNothingWithTheDocument::class, $result);
    }
}
