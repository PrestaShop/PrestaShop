<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception;

/**
 * Thrown when product packing constraints are violated
 */
class ProductPackConstraintException extends ProductPackException
{
    /**
     * When trying to pack a product which is already a pack itself
     */
    public const CANNOT_ADD_PACK_INTO_PACK = 10;

    /**
     * When product for packing quantity is invalid
     */
    public const INVALID_QUANTITY = 20;

    /**
     * When invalid pack stock type is used
     */
    public const INVALID_STOCK_TYPE = 30;

    /**
     * Code is used when trying to link a pack stock with its product and one of them has no advanced stock
     */
    public const INCOMPATIBLE_STOCK_TYPE = 40;
}
