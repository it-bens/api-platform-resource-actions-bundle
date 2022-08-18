<?php

declare(strict_types=1);

namespace ITB\ApiPlatformUpdateActionsBundle;

use ITB\ApiPlatformUpdateActionsBundle\DependencyInjection\ITBApiPlatformUpdateActionsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ITBApiPlatformUpdateActionsBundle extends Bundle
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
    public function getContainerExtension(): ITBApiPlatformUpdateActionsExtension
    {
        if (null === $this->extension) {
            $this->extension = new ITBApiPlatformUpdateActionsExtension();
        }

        return $this->extension;
    }
}
