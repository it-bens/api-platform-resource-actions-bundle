<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Console;

use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinitionCollection;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ListResourceActionsConsoleCommand extends Command
{
    private const COMMAND_NAME = 'itb:api-platform-resource-actions:list-actions';

    /**
     * @param ResourceActionDefinitionCollection $definitionCollection
     */
    public function __construct(private ResourceActionDefinitionCollection $definitionCollection)
    {
        parent::__construct(self::COMMAND_NAME);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setHelp('The command displays the configured resource actions. The actions can be filtered by their resource.');
        $this->addOption('resource', null, InputOption::VALUE_REQUIRED, 'Display only the actions for a resource.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws CompileTimeExceptionInterface
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('');

        $resource = $input->getOption('resource');

        $actionsTable = $io->createTable();
        $actionsTable->setHeaders(['API Platform resource', 'action name', 'command class', 'description']);
        $actionsTableLines = 0;
        foreach ($this->definitionCollection->getResourceActionDefinitions() as $definition) {
            // Skip actions if filter by resource is enabled.
            if (is_string($resource) && $resource !== $definition->resource) {
                continue;
            }

            $actionsTable->addRow([$definition->resource, $definition->action, $definition->command, $definition->description]);
            $actionsTableLines++;
        }

        if (0 === $actionsTableLines) {
            $noActionsMessage = !is_string($resource)
                ? 'There are no resource actions defined.'
                : sprintf('There are no resource actions defined for the resource "%s".', $resource);
            $io->info($noActionsMessage);

            return Command::SUCCESS;
        }
        $actionsTable->render();

        return Command::SUCCESS;
    }
}
