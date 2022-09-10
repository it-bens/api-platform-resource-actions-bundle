<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PublicForTestsCompilerPass implements CompilerPassInterface
{
    private const PUBLIC_SERVICE_IDS = [
        'itb_api_platform_resource_actions.resource_action_collection',
        'itb_api_platform_resource_actions.resource_action_definition_collection',
        'itb_api_platform_resource_actions.command_factory',
        'itb_api_platform_resource_actions.request_transformer',
        'itb_api_platform_resource_actions.controller',
        'itb_api_platform_resource_actions.open_api_factory',
        'api_platform.metadata.resource.metadata_factory.cached',
        'validator'
    ];

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$this->isPHPUnit()) {
            return;
        }

        foreach (self::PUBLIC_SERVICE_IDS as $serviceId) {
            $container->getDefinition($serviceId)->setPublic(true);
        }
    }

    /**
     * @return bool
     */
    private function isPHPUnit(): bool
    {
        // the constants are defined by PHPUnit
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}
