<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle;

use ITB\ApiPlatformResourceActionsBundle\DependencyInjection\ITBApiPlatformResourceActionsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ITBApiPlatformResourceActionsBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
    }

    /**
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension(): ITBApiPlatformResourceActionsExtension
    {
        if (null === $this->extension) {
            $this->extension = new ITBApiPlatformResourceActionsExtension();
        }

        return $this->extension;
    }
}
