<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Responsible for adding product to cart
 */
class AddProductToCartCommand
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
    private $quantity;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var array key-value pairs where key is customizationFieldId and value is customization field value
     */
    private $customizationsByFieldIds;

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $quantity
     * @param int|null $combinationId
     * @param array $customizationsByFieldIds
     *
     * @throws CartConstraintException
     */
    public function __construct(
        int $cartId,
        int $productId,
        int $quantity,
        ?int $combinationId = null,
        array $customizationsByFieldIds = []
    ) {
        $this->assertQtyIsPositive($quantity);
        $this->setCombinationId($combinationId);
        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->quantity = $quantity;
        $this->customizationsByFieldIds = $customizationsByFieldIds;
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return array
     */
    public function getCustomizationsByFieldIds(): array
    {
        return $this->customizationsByFieldIds;
    }

    /**
     * @param int $qty
     *
     * @throws CartConstraintException
     */
    private function assertQtyIsPositive(int $qty)
    {
        if (0 >= $qty) {
            throw new CartConstraintException(sprintf('Quantity must be positive integer. "%s" given.', $qty), CartConstraintException::INVALID_QUANTITY);
        }
    }

    /**
     * @param int|null $combinationId
     */
    private function setCombinationId(?int $combinationId)
    {
        if (null !== $combinationId) {
            $combinationId = new CombinationId($combinationId);
        }

        $this->combinationId = $combinationId;
    }
}
