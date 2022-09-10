<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Unit\Request;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContext;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use PHPUnit\Framework\TestCase;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildRequestTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class RequestTest extends TestCase
{
    use BuildRequestTrait;

    /**
     * @return Generator
     * @throws RuntimeExceptionInterface
     */
    public function provideForValidWithAllProperties(): Generator
    {
        yield ['do-nothing', [], Document::class, new ApiPlatformContext($this->buildRequestContext())];
    }

    /**
     * @return Generator
     */
    public function provideForWithMinimalProperties(): Generator
    {
        yield ['do-nothing', []];
    }

    /**
     * @dataProvider provideForValidWithAllProperties
     *
     * @param string $action
     * @param array<string, mixed> $payload
     * @param string $resource
     * @param ApiPlatformContext $context
     * @return void
     */
    public function testValidWithAllProperties(string $action, array $payload, string $resource, ApiPlatformContext $context): void
    {
        $request = new Request($action, $payload, $resource, $context);
        $this->assertInstanceOf(Request::class, $request);
    }

    /**
     * @dataProvider provideForWithMinimalProperties
     *
     * @param string $action
     * @param array<string, mixed> $payload
     * @return void
     */
    public function testValidWithMinimalProperties(string $action, array $payload): void
    {
        $request = new Request($action, $payload);
        $this->assertInstanceOf(Request::class, $request);
    }
}
