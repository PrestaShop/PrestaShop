<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception;

/**
 * Thrown when the registry fails to persist an extra property definition
 * (registration, update, or unregistration of the row and/or its SQL column).
 */
class ExtraPropertyRegistrationFailureException extends ExtraPropertyException
{
}
