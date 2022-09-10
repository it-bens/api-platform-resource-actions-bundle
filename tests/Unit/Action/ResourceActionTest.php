<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Unit\Action;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Generator;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceAction;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\CommandBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\CommandNotAClassException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\DescriptionBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\NameBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\ResourceBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\ResourceNotRegisteredException;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use PHPUnit\Framework\TestCase;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;
use Throwable;

final class ResourceActionTest extends TestCase
{
    use BuildAndBootKernelTrait;

    /**
     * @return Generator
     */
    public function provideForInvalidProperty(): Generator
    {
        $factory = $this->getResourceMetadataFactory();

        yield 'name blank' => ['', Document::class, DoNothingWithTheDocument::class, 'Nothing', $factory, NameBlankException::class];
        yield 'resource blank' => ['do-nothing', '', DoNothingWithTheDocument::class, 'Nothing', $factory, ResourceBlankException::class];
        yield 'command blank' => ['do-nothing', Document::class, '', 'Nothing', $factory, CommandBlankException::class];
        yield 'command not a class' => ['do-nothing', Document::class, 'NotARealClass', 'Nothing', $factory, CommandNotAClassException::class];
        yield 'description blank' => ['do-nothing', Document::class, DoNothingWithTheDocument::class, '', $factory, DescriptionBlankException::class];
        yield 'resource not registered' => ['do-nothing', 'NotARealClass', DoNothingWithTheDocument::class, 'Nothing', $factory, ResourceNotRegisteredException::class];
    }

    /**
     * @return Generator
     */
    public function provideForValid(): Generator
    {
        yield ['do-nothing', Document::class, DoNothingWithTheDocument::class, 'Nothing', $this->getResourceMetadataFactory()];
    }

    /**
     * @dataProvider provideForInvalidProperty
     *
     * @param string $name
     * @param string $resource
     * @param string $commandClass
     * @param string $description
     * @param ResourceMetadataFactoryInterface $resourceMetadataFactory
     * @param class-string<Throwable> $expectedException
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testInvalidProperty(
        string $name,
        string $resource,
        string $commandClass,
        string $description,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
        string $expectedException
    ): void {
        $this->expectException($expectedException);
        new ResourceAction($name, $resource, $commandClass, $description, $resourceMetadataFactory);
    }

    /**
     * @dataProvider provideForValid
     *
     * @param string $name
     * @param string $resource
     * @param string $commandClass
     * @param string $description
     * @param ResourceMetadataFactoryInterface $resourceMetadataFactory
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testValid(
        string $name,
        string $resource,
        string $commandClass,
        string $description,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ): void {
        $resourceAction = new ResourceAction($name, $resource, $commandClass, $description, $resourceMetadataFactory);
        $this->assertEquals($name, $resourceAction->getName());
        $this->assertEquals($resource, $resourceAction->getResource());
        $this->assertEquals($commandClass, $resourceAction->getCommandClass());
        $this->assertEquals($description, $resourceAction->getDescription());
    }

    /**
     * @return ResourceMetadataFactoryInterface
     */
    private function getResourceMetadataFactory(): ResourceMetadataFactoryInterface
    {
        $kernel = $this->buildKernelAndBoot('config_with_resources_and_resource_action_directories.yaml', 'api_platform_config.yaml');
        /** @var ResourceMetadataFactoryInterface $resourceMetadataFactory */
        $resourceMetadataFactory = $kernel->getContainer()->get('api_platform.metadata.resource.metadata_factory.cached');

        return $resourceMetadataFactory;
    }
}
