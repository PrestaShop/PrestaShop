<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates cart product price
 */
class UpdateProductPriceInCartCommand
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
     * @var int
     */
    private $combinationId;

    /**
     * @var float
     */
    private $price;

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $combinationId
     * @param float $price
     */
    public function __construct($cartId, $productId, $combinationId, $price)
    {
        $this->assertPriceIsPositiveFloat($price);

        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->combinationId = $combinationId;
        $this->price = $price;
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
     * @return int
     */
    public function getCombinationId()
    {
        return $this->combinationId;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    private function assertPriceIsPositiveFloat($price)
    {
        if (!is_float($price) || 0 > $price) {
            throw new CartException(sprintf('Price %s is invalid. Price must be float greater than zero.', var_export($price, true)));
        }
    }
}
