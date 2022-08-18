<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
final class UpdateRequest extends Constraint
{
    public string $actionUnknownMessage = 'The action "{{ action }}" is unknown for the resource "{{ resource }}".';
    public string $denormalizationFailedMessage = 'The command could not be crated with the provided data.';

    /**
     * @return string[]
     */
    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
