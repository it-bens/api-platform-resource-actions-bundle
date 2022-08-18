<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('itb_api_platform_update_actions');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        /** @phpstan-ignore-next-line */
        $rootNode
            ->children()
                ->booleanNode('validate_command')
                    ->defaultFalse()
                ->end()
                ->booleanNode('ignore_messenger_validation')
                    ->defaultTrue()
                ->end()
                ->arrayNode('resources')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->normalizeKeys(false)
                        ->scalarPrototype()
                            ->cannotBeEmpty()
                            ->validate()
                                ->ifTrue(static function ($commandClass) {
                                    return false === class_exists($commandClass, true);
                                })
                                ->thenInvalid('%s is not a valid class name.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
