<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Functional;

use ITB\ApiPlatformResourceActionsBundle\Context\ApiPlatformContext;
use ITB\ApiPlatformResourceActionsBundle\Exception\RuntimeExceptionInterface;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

trait BuildRequestTrait
{
    /**
     * @return Request
     * @throws RuntimeExceptionInterface
     */
    private function buildInitializedRequestToDoNothing(): Request
    {
        $document = new Document();
        $context = [
            'resource_class' => Document::class,
            AbstractNormalizer::OBJECT_TO_POPULATE => new Document(),
            'input' => ['class' => Request::class]
        ];

        return new Request('do-nothing', ['document' => $document], Document::class, new ApiPlatformContext($context));
    }

    /**
     * @return Request
     */
    private function buildUninitializedRequestToDoNothing(): Request
    {
        return new Request('do-nothing', []);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRequestContext(): array
    {
        return [
            'resource_class' => Document::class,
            AbstractNormalizer::OBJECT_TO_POPULATE => new Document(),
            'input' => ['class' => Request::class]
        ];
    }
}
