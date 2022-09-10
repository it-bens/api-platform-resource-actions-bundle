<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command;

use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class DoNothingWithTheDocument
{
    /**
     * @param Document $document
     */
    public function __construct(public Document $document)
    {
    }
}
