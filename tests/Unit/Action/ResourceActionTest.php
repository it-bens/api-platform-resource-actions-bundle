<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Unit\Action;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Generator;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceAction;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\CommandBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\CommandNotAClassException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\DescriptionBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\NameBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\NoOperationConfiguredForActionException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\ResourceBlankException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\ResourceHasNoShortNameException;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\ResourceNotRegisteredException;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;
use Tests\ITB\ApiPlatformResourceActionsBundle\Unit\ResourceMetadataTrait;
use Throwable;

final class ResourceActionTest extends TestCase
{
    use ResourceMetadataTrait;

    /**
     * @return Generator
     */
    public function provideForInvalidMetadataNoShortName(): Generator
    {
        $factory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory->method('create')->willReturn($this->createResourceMetadataWithoutShortName('patch'));

        yield ['do-nothing', Document::class, DoNothingWithTheDocument::class, 'Nothing', $factory, ResourceHasNoShortNameException::class];
    }

    /**
     * @return Generator
     * @throws ReflectionException
     */
    public function provideForInvalidNoOperationConfigured(): Generator
    {
        $factory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory->method('create')->willReturn($this->createResourceMetadataOperationWithInvalidInput(Document::class));

        yield 'operation with invalid \'input\'' => ['do-nothing', Document::class, DoNothingWithTheDocument::class, 'Nothing', $factory];

        $factory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory->method('create')->willReturn($this->createResourceMetadataOperationWithInvalidController(Document::class));

        yield  'operation with invalid \'controller\'' => ['do-nothing', Document::class, DoNothingWithTheDocument::class, 'Nothing', $factory];
    }

    /**
     * @return Generator
     * @throws ReflectionException
     */
    public function provideForInvalidProperty(): Generator
    {
        $factory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory->method('create')->willReturn($this->createResourceMetadataWithValidOperation(Document::class, 'patch'));

        yield 'name blank' => ['', Document::class, DoNothingWithTheDocument::class, 'Nothing', $factory, NameBlankException::class];
        yield 'resource blank' => ['do-nothing', '', DoNothingWithTheDocument::class, 'Nothing', $factory, ResourceBlankException::class];
        yield 'command blank' => ['do-nothing', Document::class, '', 'Nothing', $factory, CommandBlankException::class];
        yield 'command not a class' => ['do-nothing', Document::class, 'NotARealClass', 'Nothing', $factory, CommandNotAClassException::class];
        yield 'description blank' => ['do-nothing', Document::class, DoNothingWithTheDocument::class, '', $factory, DescriptionBlankException::class];
    }

    /**
     * @return Generator
     */
    public function provideForInvalidResourceNotRegistered(): Generator
    {
        $factory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory->method('create')->willThrowException(new ResourceClassNotFoundException());

        yield ['do-nothing', Document::class, DoNothingWithTheDocument::class, 'Nothing', $factory];
    }

    /**
     * @return Generator
     * @throws ReflectionException
     */
    public function provideForValid(): Generator
    {
        $factory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $factory->method('create')->willReturn($this->createResourceMetadataWithValidOperation(Document::class, 'patch'));

        yield ['do-nothing', Document::class, DoNothingWithTheDocument::class, 'Nothing', $factory];
    }

    /**
     * @dataProvider provideForInvalidNoOperationConfigured
     *
     * @param string $name
     * @param string $resource
     * @param string $commandClass
     * @param string $description
     * @param ResourceMetadataFactoryInterface $resourceMetadataFactory
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testInvalidMetadata(
        string $name,
        string $resource,
        string $commandClass,
        string $description,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ): void {
        $this->expectException(NoOperationConfiguredForActionException::class);
        new ResourceAction($name, $resource, $commandClass, $description, $resourceMetadataFactory);
    }

    /**
     * @dataProvider provideForInvalidMetadataNoShortName
     *
     * @param string $name
     * @param string $resource
     * @param string $commandClass
     * @param string $description
     * @param ResourceMetadataFactoryInterface $resourceMetadataFactory
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testInvalidMetadataNoShortName(
        string $name,
        string $resource,
        string $commandClass,
        string $description,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ): void {
        $this->expectException(ResourceHasNoShortNameException::class);
        new ResourceAction($name, $resource, $commandClass, $description, $resourceMetadataFactory);
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
     * @dataProvider provideForInvalidResourceNotRegistered
     *
     * @param string $name
     * @param string $resource
     * @param string $commandClass
     * @param string $description
     * @param ResourceMetadataFactoryInterface $resourceMetadataFactory
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testInvalidResourceNotRegistered(
        string $name,
        string $resource,
        string $commandClass,
        string $description,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ): void {
        $this->expectException(ResourceNotRegisteredException::class);
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
}
