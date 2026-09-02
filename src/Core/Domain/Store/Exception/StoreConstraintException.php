<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Exception;

/**
 * Is thrown when store is invalid
 */
class StoreConstraintException extends StoreException
{
    /**
     * Thrown when provided store id is not valid
     */
    public const INVALID_ID = 10;
}
