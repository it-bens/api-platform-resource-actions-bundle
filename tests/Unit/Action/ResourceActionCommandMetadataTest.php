<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Unit\Action;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionCommandMetadata;
use ITB\ApiPlatformResourceActionsBundle\Action\ResourceActionException\CommandNotAClassException;
use ITB\ApiPlatformResourceActionsBundle\Exception\CompileTimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use PHPUnit\Framework\TestCase;
use ReflectionParameter;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\CommandWithNoConstructor;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class ResourceActionCommandMetadataTest extends TestCase
{
    /**
     * @return Generator
     */
    public function provideForGetConstructorParameterNameForType(): Generator
    {
        yield [new ResourceActionCommandMetadata(DoNothingWithTheDocument::class)];
    }

    /**
     * @return Generator
     */
    public function provideForGetConstructorParameters(): Generator
    {
        yield [new ResourceActionCommandMetadata(DoNothingWithTheDocument::class)];
    }

    /**
     * @return Generator
     */
    public function provideForInvalidCommandClassNotAClass(): Generator
    {
        yield ['This is not a class'];
    }

    /**
     * @return Generator
     */
    public function provideForInvalidCommandWithoutConstructor(): Generator
    {
        yield [CommandWithNoConstructor::class];
    }

    /**
     * @dataProvider provideForGetConstructorParameterNameForType
     *
     * @param ResourceActionCommandMetadata $metadata
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function testGetConstructorParameterNameForType(ResourceActionCommandMetadata $metadata): void
    {
        $parameter = $metadata->getConstructorParameterNameForType(Document::class, []);
        $this->assertEquals('document', $parameter);
    }

    /**
     * @dataProvider provideForGetConstructorParameters
     *
     * @param ResourceActionCommandMetadata $metadata
     * @return void
     */
    public function testGetConstructorParameters(ResourceActionCommandMetadata $metadata): void
    {
        $this->assertCount(1, $metadata->getConstructorParameters());
        $this->assertInstanceOf(ReflectionParameter::class, $metadata->getConstructorParameters()[0]);
    }

    /**
     * @dataProvider provideForInvalidCommandClassNotAClass
     *
     * @param string $commandClass
     * @return void
     * @throws CompileTimeExceptionInterface
     */
    public function testInvalidCommandClassNotAClass(string $commandClass): void
    {
        $this->expectException(CommandNotAClassException::class);
        new ResourceActionCommandMetadata($commandClass);
    }

    /**
     * @dataProvider provideForInvalidCommandWithoutConstructor
     *
     * @param string $commandClass
     * @return void
     * @throws CompileTimeExceptionInterface
     * @throws RuntimeExceptionInterface
     */
    public function testInvalidCommandWithoutConstructor(string $commandClass): void
    {
        $metadata = new ResourceActionCommandMetadata($commandClass);
        $this->assertNull($metadata->getConstructorParameterNameForType(Document::class, []));
    }
}
