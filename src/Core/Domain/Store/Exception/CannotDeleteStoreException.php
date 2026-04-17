<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Exception;

/**
 * Thrown when cannot delete store
 */
class CannotDeleteStoreException extends StoreException
{
    public const FAILED_DELETE = 1;

    public const FAILED_BULK_DELETE = 2;
}
