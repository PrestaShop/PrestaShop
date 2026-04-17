<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Cart\Provider;

/**
 * Lightweight DTO representing a product line in a cart, as stored in ps_cart_product.
 *
 * @experimental
 */
class CartProduct
{
    public function __construct(
        protected readonly int $productId,
        protected readonly int $combinationId,
        protected readonly int $customizationId,
        protected readonly int $quantity,
    ) {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    public function getCustomizationId(): int
    {
        return $this->customizationId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
