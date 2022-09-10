<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional\Request;

use Generator;
use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContext;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use ITB\ApiPlatformResourceActionsBundle\Request\RequestTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildAndBootKernelTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Functional\BuildRequestTrait;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class RequestTransformerTest extends TestCase
{
    use BuildRequestTrait;
    use BuildAndBootKernelTrait;

    private const REQUEST_TRANSFORMER_ID = 'itb_api_platform_resource_actions.request_transformer';

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
    public function provideForSupportsTransformationValid(): Generator
    {
        yield [$this->getRequestTransformer(), $this->buildRequestContext()];
    }

    /**
     * @return Generator
     */
    public function provideForTransform(): Generator
    {
        yield [
            $this->getRequestTransformer(),
            $this->buildUninitializedRequestToDoNothing(),
            $this->buildRequestContext()
        ];
    }

    /**
     * @return Generator
     */
    public function provideSupportsTransformationInvalidDataIsAlreadyObject(): Generator
    {
        yield [$this->getRequestTransformer(), $this->buildRequestContext()];
    }

    /**
     * @return Generator
     */
    public function provideSupportsTransformationInvalidNoInputType(): Generator
    {
        $context = $this->buildRequestContext();
        unset($context['input']);

        yield [$this->getRequestTransformer(), $context];
    }

    /**
     * @dataProvider provideForInitialization
     *
     * @param KernelInterface $kernel
     * @return void
     */
    public function testInitialization(KernelInterface $kernel): void
    {
        $requestTransformer = $kernel->getContainer()->get(self::REQUEST_TRANSFORMER_ID);
        $this->assertInstanceOf(RequestTransformer::class, $requestTransformer);
    }

    /**
     * @dataProvider provideSupportsTransformationInvalidDataIsAlreadyObject
     *
     * @param RequestTransformer $requestTransformer
     * @param array<string, mixed> $context
     * @return void
     */
    public function testSupportsTransformationInvalidDataIsAlreadyObject(
        RequestTransformer $requestTransformer,
        array $context
    ): void {
        $this->assertFalse($requestTransformer->supportsTransformation(new Document(), Document::class, $context));
    }

    /**
     * @dataProvider provideSupportsTransformationInvalidNoInputType
     *
     * @param RequestTransformer $requestTransformer
     * @param array<string, mixed> $context
     * @return void
     */
    public function testSupportsTransformationInvalidNoInputType(
        RequestTransformer $requestTransformer,
        array $context
    ): void {
        $this->assertFalse($requestTransformer->supportsTransformation([], Document::class, $context));
    }

    /**
     * @dataProvider provideForSupportsTransformationValid
     *
     * @param RequestTransformer $requestTransformer
     * @param array<string, mixed> $context
     * @return void
     */
    public function testSupportsTransformationValid(RequestTransformer $requestTransformer, array $context): void
    {
        $this->assertTrue($requestTransformer->supportsTransformation([], Document::class, $context));
    }

    /**
     * @dataProvider provideForTransform
     *
     * @param RequestTransformer $requestTransformer
     * @param Request $request
     * @param array<string, mixed> $context
     * @return void
     * @throws RuntimeExceptionInterface
     */
    public function testTransform(RequestTransformer $requestTransformer, Request $request, array $context): void
    {
        /** @var Request $request */
        $request = $requestTransformer->transform($request, Document::class, $context);

        $this->assertInstanceOf(ApiPlatformContext::class, $request->apiPlatformContext);
        $this->assertEquals(Document::class, $request->resource);

        $this->assertArrayHasKey('document', $request->payload);
        $this->assertInstanceOf(Document::class, $request->payload['document']);
    }

    /**
     * @return RequestTransformer
     */
    private function getRequestTransformer(): RequestTransformer
    {
        $kernel = $this->buildKernelAndBoot(
            'config_with_resources_and_resource_action_directories.yaml',
            'api_platform_config.yaml'
        );
        /** @var RequestTransformer $requestTransformer */
        $requestTransformer = $kernel->getContainer()->get(self::REQUEST_TRANSFORMER_ID);

        return $requestTransformer;
    }
}
