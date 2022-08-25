<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Exception;

use Throwable;

/**
 * All exceptions of this bundle, that can occur at runtime, are implementing this interface.
 * If thrown, they should be wrapped with a API Platform expected exception.
 */
interface RuntimeExceptionInterface extends Throwable
{
    /**
     * @return Throwable
     */
    public function createApiPlatformCompatibleException(): Throwable;
}
