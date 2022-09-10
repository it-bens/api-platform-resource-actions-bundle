<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Command;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Command\CommandFactory;
use ITB\ApiPlatformResourceActionsBundle\Command\CommandFactoryException\RequestResourceIsNullException;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContext;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildRequestTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;

final class CommandFactoryTest extends TestCase
{
    use BuildRequestTrait;
    use BuildAndBootKernelTrait;

    private const COMMAND_FACTORY_ID = 'itb_api_platform_resource_actions.command_factory';

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForCreateCommandInvalidResourceMissing(): Generator
    {
        $request = $this->buildInitializedRequestToDoNothing();
        $request->resource = null;

        yield [$this->getCommandFactory(), $request];
    }

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForCreateCommandValid(): Generator
    {
        yield [$this->getCommandFactory(), $this->buildInitializedRequestToDoNothing()];
    }

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
     * @dataProvider provideForCreateCommandInvalidResourceMissing
     *
     * @param CommandFactory $commandFactory
     * @param Request $request
     * @return void
     * @throws ExceptionInterface
     * @throws RuntimeExceptionInterface
     */
    public function testCreateCommandInvalidResourceMissing(CommandFactory $commandFactory, Request $request): void
    {
        $this->expectException(RequestResourceIsNullException::class);
        $commandFactory->createCommand($request);
    }

    /**
     * @dataProvider provideForCreateCommandValid
     *
     * @param CommandFactory $commandFactory
     * @param Request $request
     * @return void
     * @throws ExceptionInterface
     * @throws RuntimeExceptionInterface
     */
    public function testCreateCommandValid(CommandFactory $commandFactory, Request $request): void
    {
        /** @var DoNothingWithTheDocument $command */
        $command = $commandFactory->createCommand($request);
        $this->assertInstanceOf(DoNothingWithTheDocument::class, $command);

        $apiPlatformContext = $request->apiPlatformContext;
        $this->assertInstanceOf(ApiPlatformContext::class, $apiPlatformContext);
        /** @var ApiPlatformContext $apiPlatformContext */
        $this->assertEquals($apiPlatformContext->getResourceObject(), $command->document);
    }

    /**
     * @dataProvider provideForInitialization
     *
     * @param KernelInterface $kernel
     * @return void
     */
    public function testInitialization(KernelInterface $kernel): void
    {
        $commandFactory = $kernel->getContainer()->get(self::COMMAND_FACTORY_ID);
        $this->assertInstanceOf(CommandFactory::class, $commandFactory);
    }

    /**
     * @return CommandFactory
     */
    private function getCommandFactory(): CommandFactory
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );
        /** @var CommandFactory $commandFactory */
        $commandFactory = $kernel->getContainer()->get(self::COMMAND_FACTORY_ID);

        return $commandFactory;
    }
}
