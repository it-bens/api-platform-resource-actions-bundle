<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Definition;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinition;
use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinitionCollection;
use ITB\ApiPlatformResourceActionsBundle\Definition\ResourceActionDefinitionCollectionException\ResourceActionNotUniqueException;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class ResourceActionDefinitionCollectionTest extends TestCase
{
    use BuildAndBootKernelTrait;

    private const ACTION_DEFINITION_COLLECTION_ID = 'itb_api_platform_resource_actions.resource_action_definition_collection';

    /**
     * @return Generator
     */
    public function provideForTestWithResourceInBundleConfiguration(): Generator
    {
        $kernel = $this->buildKernelAndBoot('config_with_resources.yaml');
        $resourceActionDefinitionCollection = $kernel->getContainer()->get(self::ACTION_DEFINITION_COLLECTION_ID);

        yield [$resourceActionDefinitionCollection];
    }

    /**
     * @return Generator
     */
    public function provideForTestWithResourcesInAttributes(): Generator
    {
        $kernel = $this->buildKernelAndBoot('config_with_resource_action_directories.yaml');
        $resourceActionDefinitionCollection = $kernel->getContainer()->get(self::ACTION_DEFINITION_COLLECTION_ID);

        yield [$resourceActionDefinitionCollection];
    }

    /**
     * @return Generator
     */
    public function provideForTestWithDuplicateResources(): Generator
    {
        $kernel = $this->buildKernelAndBoot('config_with_duplicate_resources.yaml');
        $resourceActionDefinitionCollection = $kernel->getContainer()->get(self::ACTION_DEFINITION_COLLECTION_ID);

        yield [$resourceActionDefinitionCollection];
    }

    /**
     * @return Generator
     */
    public function provideForTestWithResourcesInBundleConfigurationAndAttributes(): Generator
    {
        $kernel = $this->buildKernelAndBoot('config_with_resources_and_resource_action_directories.yaml');
        $resourceActionDefinitionCollection = $kernel->getContainer()->get(self::ACTION_DEFINITION_COLLECTION_ID);

        yield [$resourceActionDefinitionCollection];
    }

    /**
     * @return Generator
     */
    public function provideKernelWithResourcesInBundleConfigurationAndAttributes(): Generator
    {
        $kernel = $this->buildKernelAndBoot('config_with_resources_and_resource_action_directories.yaml');

        yield [$kernel];
    }

    /**
     * @dataProvider provideKernelWithResourcesInBundleConfigurationAndAttributes
     *
     * @param KernelInterface $kernel
     * @return void
     */
    public function testInitializationWithResourcesInBundleConfigurationAndAttributes(KernelInterface $kernel): void
    {
        $resourceActionDefinitionCollection = $kernel->getContainer()->get(self::ACTION_DEFINITION_COLLECTION_ID);

        $this->assertInstanceOf(ResourceActionDefinitionCollection::class, $resourceActionDefinitionCollection);
    }

    /**
     * @dataProvider provideForTestWithResourceInBundleConfiguration
     *
     * @param ResourceActionDefinitionCollection $definitionCollection
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testValidWithResourceInBundleConfiguration(ResourceActionDefinitionCollection $definitionCollection): void
    {
        $definitions = $definitionCollection->getResourceActionDefinitions();
        $this->assertCount(1, $definitions);

        $this->assertInstanceOf(ResourceActionDefinition::class, $definitions[0]);
        $this->assertEquals(Document::class, $definitions[0]->resource);
        $this->assertEquals('do-nothing', $definitions[0]->action);
        $this->assertEquals(DoNothingWithTheDocument::class, $definitions[0]->command);
        $this->assertEquals(null, $definitions[0]->description);
    }

    /**
     * @dataProvider provideForTestWithResourcesInAttributes
     *
     * @param ResourceActionDefinitionCollection $definitionCollection
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testValidWithResourcesInAttributes(ResourceActionDefinitionCollection $definitionCollection): void
    {
        $definitions = $definitionCollection->getResourceActionDefinitions();
        $this->assertCount(1, $definitions);

        $this->assertInstanceOf(ResourceActionDefinition::class, $definitions[0]);
        $this->assertEquals(Document::class, $definitions[0]->resource);
        $this->assertEquals('do-also-nothing', $definitions[0]->action);
        $this->assertEquals(DoNothingWithTheDocument::class, $definitions[0]->command);
        $this->assertEquals(null, $definitions[0]->description);
    }

    /**
     * @dataProvider provideForTestWithResourcesInBundleConfigurationAndAttributes
     *
     * @param ResourceActionDefinitionCollection $definitionCollection
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testValidWithResourcesInBundleConfigurationAndAttributes(
        ResourceActionDefinitionCollection $definitionCollection
    ): void {
        $definitions = $definitionCollection->getResourceActionDefinitions();
        $this->assertCount(2, $definitions);

        foreach ($definitions as $definition) {
            $this->assertInstanceOf(ResourceActionDefinition::class, $definition);
            $this->assertEquals(Document::class, $definition->resource);
        }
    }

    /**
     * @dataProvider provideForTestWithDuplicateResources
     *
     * @param ResourceActionDefinitionCollection $definitionCollection
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testInvalidWithDuplicateResources(ResourceActionDefinitionCollection $definitionCollection): void
    {
        $this->expectException(ResourceActionNotUniqueException::class);
        $definitionCollection->getResourceActionDefinitions();
    }
}
