<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Removes given product from cart.
 */
class RemoveProductFromCartCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var int|null
     */
    private $combinationId;

    /**
     * @var int|null
     */
    private $customizationId;

    /**
     * @param int $cartId
     * @param int $productId
     * @param int|null $combinationId
     * @param int|null $customizationId
     *
     * @throws CartConstraintException
     */
    public function __construct(
        int $cartId,
        int $productId,
        ?int $combinationId = null,
        ?int $customizationId = null
    ) {
        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->combinationId = $combinationId;
        $this->customizationId = $customizationId;
    }

    /**
     * @return CartId
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int|null
     */
    public function getCombinationId()
    {
        return $this->combinationId;
    }

    /**
     * @return int|null
     */
    public function getCustomizationId()
    {
        return $this->customizationId;
    }
}
