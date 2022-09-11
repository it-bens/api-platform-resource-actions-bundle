<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle;

use ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle;
use Exception;
use ITB\ApiPlatformResourceActionsBundle\ITBApiPlatformResourceActionsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;

final class ITBApiPlatformResourceActionsBundleKernel extends Kernel
{
    /** @var string */
    private string $cacheDirectory;

    /**
     * @param string $environment
     * @param bool $debug
     * @param array<string, mixed>|null $resourceActionBundleConfig
     * @param array<string, mixed>|null $apiPlatformBundleConfig
     */
    public function __construct(
        string $environment,
        bool $debug,
        private ?array $resourceActionBundleConfig = null,
        private ?array $apiPlatformBundleConfig = null
    ) {
        parent::__construct($environment, $debug);

        $this->cacheDirectory = __DIR__ . '/../var/cache/' . spl_object_hash($this);
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDirectory;
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new ApiPlatformBundle(),
            new ITBApiPlatformResourceActionsBundle()
        ];
    }

    /**
     * @param LoaderInterface $loader
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension(
                'framework',
                Yaml::parseFile(__DIR__ . '/Fixtures/Configuration/framework.yaml')
            );

            if (null !== $this->apiPlatformBundleConfig) {
                $container->loadFromExtension('api_platform', $this->apiPlatformBundleConfig);
            }
            if (null !== $this->resourceActionBundleConfig) {
                $container->loadFromExtension('itb_api_platform_resource_actions', $this->resourceActionBundleConfig);
            }

            // Tested services are made public to use them via container.
            $container->addCompilerPass(new PublicForTestsCompilerPass());
        });
    }
}
