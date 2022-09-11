<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Unit\Context;

use ApiPlatform\Core\Bridge\Symfony\Messenger\ContextStamp;
use Generator;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContext;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException\ObjectToPopulateMissingException;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException\ObjectToPopulateNotAnObjectException;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException\ResourceClassMissingException;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContextException\ResourceClassNotAStringException;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildRequestTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;
use Throwable;

final class ApiPlatformContextTest extends TestCase
{
    use BuildRequestTrait;

    /**
     * @return Generator
     */
    public function provideForInvalidProperty(): Generator
    {
        $context = $this->buildRequestContext();
        unset($context['resource_class']);
        yield 'resource class missing' => [$context, ResourceClassMissingException::class];

        $context = $this->buildRequestContext();
        $context['resource_class'] = 1337;
        yield 'resource class not a string' => [$context, ResourceClassNotAStringException::class];

        $context = $this->buildRequestContext();
        unset($context[AbstractNormalizer::OBJECT_TO_POPULATE]);
        yield 'object to populate missing' => [$context, ObjectToPopulateMissingException::class];

        $context = $this->buildRequestContext();
        $context[AbstractNormalizer::OBJECT_TO_POPULATE] = 'Not an object';
        yield 'object to populate not an object' => [$context, ObjectToPopulateNotAnObjectException::class];
    }

    /**
     * @return Generator
     */
    public function provideForValid(): Generator
    {
        yield [$this->buildRequestContext()];
    }

    /**
     * @dataProvider provideForInvalidProperty
     *
     * @param array<string, mixed> $context
     * @param class-string<Throwable> $expectedException
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function testInvalidProperty(array $context, string $expectedException): void
    {
        $this->expectException($expectedException);
        new ApiPlatformContext($context);
    }

    /**
     * @dataProvider provideForValid
     *
     * @param array<string, mixed> $context
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function testValid(array $context): void
    {
        $apiPlatformContext = new ApiPlatformContext($context);
        $this->assertEquals(Document::class, $apiPlatformContext->getResourceClass());
        $this->assertEquals($context[AbstractNormalizer::OBJECT_TO_POPULATE], $apiPlatformContext->getResourceObject());
    }
}
