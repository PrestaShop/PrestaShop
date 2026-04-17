<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception;

/**
 * Thrown when product supplier constraints are violated
 */
class ProductSupplierConstraintException extends ProductSupplierException
{
    /**
     * When product supplier id is invalid
     */
    public const INVALID_ID = 10;

    /**
     * When product supplier reference is invalid
     */
    public const INVALID_REFERENCE = 20;

    /**
     * When product supplier price is invalid
     */
    public const INVALID_PRICE = 30;
}
