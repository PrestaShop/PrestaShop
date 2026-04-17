<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Exception;

/**
 * Thrown when fails to delete supplier
 */
class CannotDeleteSupplierException extends SupplierException
{
    /**
     * When fails to delete supplier due to existing pending orders of that supplier
     */
    public const HAS_PENDING_ORDERS = 1;

    /**
     * When fails to delete single supplier
     */
    public const FAILED_DELETE = 2;

    /**
     * When fails to delete supplier in bulk action
     */
    public const FAILED_BULK_DELETE = 3;
}
