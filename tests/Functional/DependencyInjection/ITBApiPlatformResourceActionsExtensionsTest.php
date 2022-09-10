<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\DependencyInjection;

use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use Tests\ITB\ApiPlatformResourceActionsBundle\ITBApiPlatformResourceActionsBundleKernel;

final class ITBApiPlatformResourceActionsExtensionsTest extends TestCase
{
    /**
     * @return Generator
     */
    public function provide(): Generator
    {
        $resourceActionBundleConfigFilePath = __DIR__ . '/../../Fixtures/Configuration/config_with_resources_and_resource_action_directories.yaml';
        $resourceActionBundleConfig = Yaml::parseFile($resourceActionBundleConfigFilePath);
        $apiPlatformBundleConfigFilePath = __DIR__ . '/../../Fixtures/Configuration/api_platform_config.yaml';
        $apiPlatformBundleConfig = Yaml::parseFile($apiPlatformBundleConfigFilePath);
        yield [$resourceActionBundleConfig, $apiPlatformBundleConfig];
    }

    /**
     * @dataProvider provide
     *
     * @param array<string, mixed> $resourceActionBundleConfig
     * @param array<string, mixed> $apiPlatformBundleConfig
     * @return void
     */
    public function test(array $resourceActionBundleConfig, array $apiPlatformBundleConfig): void
    {
        $kernel = new ITBApiPlatformResourceActionsBundleKernel('prod', false, $resourceActionBundleConfig, $apiPlatformBundleConfig);
        $kernel->boot();
        $container = $kernel->getContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }
}
