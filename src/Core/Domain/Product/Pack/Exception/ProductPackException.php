<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;

/**
 * Thrown when some product packing actions fails, also base exception of Product/Pack subdomain
 */
class ProductPackException extends ProductException
{
    /**
     * When fails to add product to pack
     */
    public const FAILED_ADDING_TO_PACK = 10;

    /**
     * When fails to delete products from a pack
     */
    public const FAILED_DELETING_PRODUCTS_FROM_PACK = 20;
}
