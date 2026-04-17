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
 * Adds product customization
 */
class AddCustomizationCommand
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
     * @var array key - value pairs where key is the id of customization field and the value is the customization value
     */
    private $customizationValuesByFieldIds;

    /**
     * @param int $cartId
     * @param int $productId
     * @param array $customizationValuesByFieldIds
     *
     * @throws CartConstraintException
     */
    public function __construct(int $cartId, int $productId, array $customizationValuesByFieldIds)
    {
        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->customizationValuesByFieldIds = $customizationValuesByFieldIds;
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
     * @return array
     */
    public function getCustomizationValuesByFieldIds(): array
    {
        return $this->customizationValuesByFieldIds;
    }
}
