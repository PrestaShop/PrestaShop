<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception;

/**
 * Thrown when product stock constraints are violated
 */
class ProductStockConstraintException extends ProductStockException
{
    /**
     * Code is sent when invalid out of stock type is used
     */
    public const INVALID_OUT_OF_STOCK_TYPE = 10;

    /**
     * When quantity is invalid
     */
    public const INVALID_QUANTITY = 20;

    /**
     * When location is invalid
     */
    public const INVALID_LOCATION = 30;

    /**
     * When out_of_stock is invalid
     */
    public const INVALID_OUT_OF_STOCK = 40;

    /**
     * When id is invalid
     */
    public const INVALID_ID = 50;

    /**
     * When delta quantity is invalid
     */
    public const INVALID_DELTA_QUANTITY = 60;

    /**
     * When fixed quantity and delta quantity are both provided
     */
    public const FIXED_AND_DELTA_QUANTITY_PROVIDED = 70;
}
