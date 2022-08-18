<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Request;

final class Request
{
    /**
     * The DTOs properties are not immutable because it's not a domain object and coupled to Api Platform.
     * This bundle requires the DTO to be mutable.
     *
     * @param string $action
     * @param array<string, mixed> $payload
     * @param string|null $resource
     */
    public function __construct(public string $action, public array $payload, public ?string $resource = null)
    {
    }
}