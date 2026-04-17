<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Exception;

/**
 * Thrown when cannot toggle store status
 */
class CannotToggleStoreStatusException extends StoreException
{
    /**
     * Thrown when cannot toggle single store status.
     */
    public const SINGLE_TOGGLE = 10;

    /**
     * Thrown when cannot bulk toggle stores status.
     */
    public const BULK_TOGGLE = 20;
}
