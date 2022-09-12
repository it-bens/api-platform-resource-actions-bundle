<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Console;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Console\ListResourceActionsConsoleCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\AnotherDocument;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class ListResourceActionsConsoleCommandTest extends TestCase
{
    use BuildAndBootKernelTrait;

    private const CONSOLE_COMMAND_ID = 'itb_api_platform_resource_actions.list_resource_actions_console_command';
    private const CONSOLE_COMMAND_BASE_OUTPUT_LINES = 9;

    /**
     * @return Generator
     */
    public function provideForExecuteListAll(): Generator
    {
        $consoleCommand = $this->getConsoleCommand();
        $commandTester = new CommandTester($consoleCommand);

        yield [$commandTester];
    }

    /**
     * @return Generator
     */
    public function provideForExecuteListForResourceWithActions(): Generator
    {
        $consoleCommand = $this->getConsoleCommand();
        $commandTester = new CommandTester($consoleCommand);

        yield [$commandTester, Document::class, 2];
    }

    /**
     * @return Generator
     */
    public function provideForExecuteListForResourceWithoutActions(): Generator
    {
        $consoleCommand = $this->getConsoleCommand();
        $commandTester = new CommandTester($consoleCommand);

        yield [$commandTester, AnotherDocument::class];
    }

    /**
     * @return Generator
     */
    public function provideForExecuteListNoActions(): Generator
    {
        $consoleCommand = $this->getConsoleCommandWithNoActions();
        $commandTester = new CommandTester($consoleCommand);

        yield [$commandTester];
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
     * @dataProvider provideForExecuteListAll
     *
     * @param CommandTester $commandTester
     * @return void
     */
    public function testExecuteListAll(CommandTester $commandTester): void
    {
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $outputLines = preg_split("/\r\n|\n|\r/", $output);

        // Check header
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString('API Platform resource', $outputLines[5]);
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString('action name', $outputLines[5]);
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString('command class', $outputLines[5]);
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString('description', $outputLines[5]);

        // Check action 1
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString(Document::class, $outputLines[7]);
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString('do-nothing', $outputLines[7]);
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString(DoNothingWithTheDocument::class, $outputLines[7]);
        // Check action 2
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString(Document::class, $outputLines[8]);
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString('do-also-nothing', $outputLines[8]);
        /** @phpstan-ignore-next-line */
        $this->assertStringContainsString(DoNothingWithTheDocument::class, $outputLines[8]);
    }

    /**
     * @dataProvider provideForExecuteListForResourceWithActions
     *
     * @param CommandTester $commandTester
     * @param string $resource
     * @param int $expectedActionsCount
     * @return void
     */
    public function testExecuteListForResourceWithActions(CommandTester $commandTester, string $resource, int $expectedActionsCount): void
    {
        $commandTester->execute(['--resource' => $resource]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $outputLines = preg_split("/\r\n|\n|\r/", $output);
        /** @phpstan-ignore-next-line */
        $this->assertCount(self::CONSOLE_COMMAND_BASE_OUTPUT_LINES + $expectedActionsCount, $outputLines);
    }

    /**
     * @dataProvider provideForExecuteListForResourceWithoutActions
     *
     * @param CommandTester $commandTester
     * @param string $resource
     * @return void
     */
    public function testExecuteListForResourceWithoutActions(CommandTester $commandTester, string $resource): void
    {
        $commandTester->execute(['--resource' => $resource]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString($resource, $output);
    }

    /**
     * @dataProvider provideForExecuteListNoActions
     *
     * @param CommandTester $commandTester
     * @return void
     */
    public function testExecuteListNoActions(CommandTester $commandTester): void
    {
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('There are no resource actions defined.', $output);
    }

    /**
     * @dataProvider provideForInitialization
     *
     * @param KernelInterface $kernel
     * @return void
     */
    public function testInitialization(KernelInterface $kernel): void
    {
        $consoleCommand = $kernel->getContainer()->get(self::CONSOLE_COMMAND_ID);
        $this->assertInstanceOf(ListResourceActionsConsoleCommand::class, $consoleCommand);
    }

    /**
     * @return ListResourceActionsConsoleCommand
     */
    private function getConsoleCommand(): ListResourceActionsConsoleCommand
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );
        /** @var ListResourceActionsConsoleCommand $consoleCommand */
        $consoleCommand = $kernel->getContainer()->get(self::CONSOLE_COMMAND_ID);

        return $consoleCommand;
    }

    /**
     * @return ListResourceActionsConsoleCommand
     */
    private function getConsoleCommandWithNoActions(): ListResourceActionsConsoleCommand
    {
        $kernel = $this->buildKernelAndBoot(
            'config_without_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );
        /** @var ListResourceActionsConsoleCommand $consoleCommand */
        $consoleCommand = $kernel->getContainer()->get(self::CONSOLE_COMMAND_ID);

        return $consoleCommand;
    }
}
