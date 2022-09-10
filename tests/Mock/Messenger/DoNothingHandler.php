<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Messenger;

use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity\Document;

final class DoNothingHandler
{
    /**
     * @param DoNothingWithTheDocument $command
     * @return Document
     */
    public function __invoke(DoNothingWithTheDocument $command): Document
    {
        return $command->document;
    }
}
