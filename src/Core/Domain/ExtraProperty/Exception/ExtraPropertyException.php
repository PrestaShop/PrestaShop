<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Base exception for extra property domain operations.
 *
 * Thrown when an extra property command or query cannot be fulfilled
 * (e.g. invalid entity name, entity not found, write failure).
 */
class ExtraPropertyException extends DomainException
{
}
