<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates product quantity in cart
 * Quantity given must include gift product
 */
class UpdateProductQuantityInCartCommand
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
    private $newQuantity;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var CustomizationId|null
     */
    private $customizationId;

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $quantity
     * @param int|null $combinationId
     * @param int|null $customizationId
     *
     * @throws CartConstraintException
     * @throws CartException
     */
    public function __construct(
        $cartId,
        $productId,
        $quantity,
        $combinationId = null,
        $customizationId = null
    ) {
        $this->setCombinationId($combinationId);
        $this->setCustomizationId($customizationId);
        $this->assertQuantityIsPositive($quantity);

        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->newQuantity = $quantity;
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
    public function getNewQuantity()
    {
        return $this->newQuantity;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return CustomizationId|null
     */
    public function getCustomizationId(): ?CustomizationId
    {
        return $this->customizationId;
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

    /**
     * @param int|null $customizationId
     */
    private function setCustomizationId(?int $customizationId)
    {
        if (null !== $customizationId) {
            $customizationId = new CustomizationId($customizationId);
        }

        $this->customizationId = $customizationId;
    }

    /**
     * @param int $qty
     *
     * @throws CartConstraintException
     */
    private function assertQuantityIsPositive(int $qty)
    {
        if (0 >= $qty) {
            throw new CartConstraintException(sprintf('Quantity must be positive integer. "%s" given.', $qty), CartConstraintException::INVALID_QUANTITY);
        }
    }
}
