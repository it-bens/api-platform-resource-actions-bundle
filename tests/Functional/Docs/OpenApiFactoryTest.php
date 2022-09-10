<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Docs;

use ApiPlatform\Core\OpenApi\OpenApi;
use Generator;
use ITB\ApiPlatformResourceActionsBundle\Docs\OpenApiFactory;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;

final class OpenApiFactoryTest extends TestCase
{
    use BuildAndBootKernelTrait;

    private const OPENAPI_FACTORY_ID = 'itb_api_platform_resource_actions.open_api_factory';

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
     */
    public function provideForOperationDescriptions(): Generator
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );
        /** @var OpenApiFactory $openApiFactory */
        $openApiFactory = $kernel->getContainer()->get(self::OPENAPI_FACTORY_ID);

        yield [$openApiFactory];
    }

    /**
     * @dataProvider provideForInitialization
     *
     * @param KernelInterface $kernel
     * @return void
     */
    public function testInitialization(KernelInterface $kernel): void
    {
        $openApiFactory = $kernel->getContainer()->get(self::OPENAPI_FACTORY_ID);
        $this->assertInstanceOf(OpenApiFactory::class, $openApiFactory);
    }

    /**
     * @dataProvider provideForOperationDescriptions
     *
     * @param OpenApiFactory $openApiFactory
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testOperationDescriptions(OpenApiFactory $openApiFactory): void
    {
        $openApi = $openApiFactory();
        $this->assertInstanceOf(OpenApi::class, $openApi);

        $operationDescription = $openApi->getPaths()->getPaths()['/api/documents']->getPatch()->getDescription();
        $this->assertEquals(1, substr_count($operationDescription, '## Actions'));
        $this->assertEquals(1, substr_count($operationDescription, 'do-nothing'));
        $this->assertEquals(1, substr_count($operationDescription, 'do-also-nothing'));
    }
}
