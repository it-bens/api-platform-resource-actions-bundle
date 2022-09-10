<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;
use Tests\ITB\ApiPlatformResourceActionsBundle\ITBApiPlatformResourceActionsBundleKernel;

trait BuildAndBootKernelTrait
{
    /**
     * @param string $resourceActionBundleConfigFile
     * @param string|null $apiPlatformBundleConfigFile
     * @return KernelInterface
     */
    private function buildKernelAndBoot(
        string $resourceActionBundleConfigFile,
        ?string $apiPlatformBundleConfigFile = null
    ): KernelInterface {
        $resourceActionBundleConfigFilePath = __DIR__ . '/../Fixtures/Configuration/' . $resourceActionBundleConfigFile;
        $resourceActionBundleConfig = Yaml::parseFile($resourceActionBundleConfigFilePath);

        $apiPlatformBundleConfigFilePath = null !== $apiPlatformBundleConfigFile ? __DIR__ . '/../Fixtures/Configuration/' . $apiPlatformBundleConfigFile : null;
        $apiPlatformBundleConfig = null !== $apiPlatformBundleConfigFilePath ? Yaml::parseFile($apiPlatformBundleConfigFilePath) : null;

        $kernel = new ITBApiPlatformResourceActionsBundleKernel(
            'test',
            true,
            $resourceActionBundleConfig,
            $apiPlatformBundleConfig
        );
        $kernel->boot();

        return $kernel;
    }
}
