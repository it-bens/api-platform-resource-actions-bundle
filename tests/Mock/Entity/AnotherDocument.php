<?php

declare(strict_types=1);

namespace Tests\ITB\ApiPlatformResourceActionsBundle\Mock\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ITB\ApiPlatformResourceActionsBundle\Controller\Controller;
use ITB\ApiPlatformResourceActionsBundle\Request\Request;

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
final class AnotherDocument
{
    #[ApiProperty(identifier: true)]
    public string $anotherName;

    public function __construct(string $anotherName = 'test')
    {
        $this->anotherName = $anotherName;
    }
}
