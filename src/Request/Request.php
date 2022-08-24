<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Request;

use ITB\ApiPlatformUpdateActionsBundle\Context\ApiPlatformContext;
use ITB\ApiPlatformUpdateActionsBundle\Validation\UpdateRequest;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class Request
{
    /**
     * The DTOs properties are not immutable because it's not a domain object and coupled to Api Platform.
     * This bundle requires the DTO to be mutable.
     *
     * @param string $action
     * @param array<string, mixed> $payload
     * @param string|null $resource
     * @param ApiPlatformContext|null $apiPlatformContext
     */
    public function __construct(
        public string $action,
        public array $payload,
        public ?string $resource = null,
        public ?ApiPlatformContext $apiPlatformContext = null
    ) {
    }

    /**
     * @param ClassMetadata $metadata
     * @return void
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(new UpdateRequest());
    }
}
