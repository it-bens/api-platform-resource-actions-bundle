<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Controller;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Controller\Controller;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
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
