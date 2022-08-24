<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class ITBApiPlatformUpdateActionsExtension extends Extension
{
    /**
     * @return string
     */
    public function getAlias(): string
    {
        return 'itb_api_platform_update_actions';
    }

    /**
     * The container configuration will fail if either api-platform, messenger, serializer or validator are not configured.
     *
     * @param array<string|int, mixed> $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $actionsData = [];
        foreach ($config['resources'] as $resource => $actions) {
            foreach ($actions as $action => $actionData) {
                $actionsData[] = [
                    'resource' => $resource,
                    'action' => $action,
                    'commandClass' => $actionData['command_class'],
                    'description' => $actionData['description']
                ];
            }
        }
        $actionCollection = $container->getDefinition('itb_api_platform_update_actions.action_collection');
        $actionCollection->replaceArgument(0, $actionsData);

        $controller = $container->getDefinition('itb_api_platform_update_actions.controller');
        $controller->replaceArgument(3, $config['validate_command']);
        $controller->replaceArgument(4, $config['ignore_messenger_validation']);
    }
}
