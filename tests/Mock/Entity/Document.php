<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ITB\ApiPlatformResourceActionsBundle\Attribute\ResourceAction;
use ITB\ApiPlatformResourceActionsBundle\Controller\Controller;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Command\DoNothingWithTheDocument;

#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'patch' => [
            'method' => 'patch',
            'input' => Request::class,
            'controller' => Controller::class
        ]
    ]
)]
#[ResourceAction('do-also-nothing', DoNothingWithTheDocument::class)]
final class Document
{
    #[ApiProperty(identifier: true)]
    public string $name;

    public function __construct(string $name = 'test')
    {
        $this->name = $name;
    }
}
