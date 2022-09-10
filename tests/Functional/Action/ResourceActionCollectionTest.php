<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Action;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceAction;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionCollection;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionCollectionException\ActionForResourceNotFound;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class ResourceActionCollectionTest extends TestCase
{
    use BuildAndBootKernelTrait;

    private const ACTION_COLLECTION_ID = 'itb_api_platform_resource_actions.resource_action_collection';

    /**
     * @return Generator
     */
    public function provideResourceActionCollectionWithResourcesFromBundleConfigurationAndAttributes(): Generator
    {
        $kernel = $this->provideKernelWithResourcesFromBundleConfigurationAndAttributes()->current()[0];
        $resourceActionCollection = $kernel->getContainer()->get(self::ACTION_COLLECTION_ID);

        yield [$resourceActionCollection];
    }

    /**
     * @return Generator
     */
    public function provideKernelWithResourcesFromBundleConfigurationAndAttributes(): Generator
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );

        yield [$kernel];
    }

    /**
     * @dataProvider provideKernelWithResourcesFromBundleConfigurationAndAttributes
     *
     * @param KernelInterface $kernel
     * @return void
     */
    public function testInitialization(KernelInterface $kernel): void
    {
        $resourceActionCollection = $kernel->getContainer()->get(self::ACTION_COLLECTION_ID);

        $this->assertInstanceOf(ResourceActionCollection::class, $resourceActionCollection);
    }

    /**
     * @dataProvider provideResourceActionCollectionWithResourcesFromBundleConfigurationAndAttributes
     *
     * @param ResourceActionCollection $actionCollection
     * @return void
     */
    public function testGetActions(ResourceActionCollection $actionCollection): void
    {
        $resourceActions = $actionCollection->getActions();
        $this->assertCount(2, $resourceActions);
        foreach ($resourceActions as $resourceAction) {
            $this->assertInstanceOf(ResourceAction::class, $resourceAction);
            $this->assertEquals(Document::class, $resourceAction->getResource());
            $this->assertEquals(DoNothingWithTheDocument::class, $resourceAction->getCommandClass());
            $this->assertEquals('patch', $resourceAction->getOperationName());
        }
    }

    /**
     * @dataProvider provideResourceActionCollectionWithResourcesFromBundleConfigurationAndAttributes
     *
     * @param ResourceActionCollection $actionCollection
     * @return void
     */
    public function testGetActionsForResource(ResourceActionCollection $actionCollection): void
    {
        $resourceActions = $actionCollection->getActionsForResource(Document::class);
        $this->assertCount(2, $resourceActions);
    }

    /**
     * @dataProvider provideResourceActionCollectionWithResourcesFromBundleConfigurationAndAttributes
     *
     * @param ResourceActionCollection $actionCollection
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function testGetActionValid(ResourceActionCollection $actionCollection): void
    {
        $resourceAction = $actionCollection->getAction(Document::class, 'do-nothing');
        $this->assertEquals('do-nothing', $resourceAction->getName());
    }

    /**
     * @dataProvider provideResourceActionCollectionWithResourcesFromBundleConfigurationAndAttributes
     *
     * @param ResourceActionCollection $actionCollection
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function testGetActionInvalidUnknownAction(ResourceActionCollection $actionCollection): void
    {
        $this->expectException(ActionForResourceNotFound::class);
        $actionCollection->getAction(Document::class, 'do-something');
    }
}
