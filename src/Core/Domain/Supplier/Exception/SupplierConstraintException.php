<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Exception;

/**
 * Is thrown when supplier constraints are violated
 */
class SupplierConstraintException extends SupplierException
{
    /**
     * When invalid data is provided for bulk action
     */
    public const INVALID_BULK_DATA = 1;
}
