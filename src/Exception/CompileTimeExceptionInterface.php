<?php

declare(strict_types=1);

namespace ITB\ApiPlatformResourceActionsBundle\Exception;

use Throwable;

/**
 * All exceptions of this bundle, that can occur at configuration/container-building time, are implementing this interface.
 * They can be safely thrown as themself to indicate a problem related to this bundle.
 */
interface CompileTimeExceptionInterface extends Throwable
{
}
