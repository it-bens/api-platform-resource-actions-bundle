<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command;

use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class DoNothingWithTheDocumentAndAnother
{
    /**
     * @param Document $document
     * @param Document $anotherDocument
     */
    public function __construct(public Document $document, public Document $anotherDocument)
    {
    }
}
