<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Query;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Query for getting cart information
 */
class GetCartForOrderCreation
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var bool
     */
    private $hideDiscounts = false;

    /**
     * @param int $cartId
     *
     * @throws CartConstraintException
     */
    public function __construct(int $cartId)
    {
        $this->cartId = new CartId($cartId);
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return bool
     */
    public function hideDiscounts(): bool
    {
        return $this->hideDiscounts;
    }

    /**
     * When hideDiscounts is set to TRUE,
     * Gift products are in a separate line from other products which are charged for
     * The price of any gift products is not included in the overall discounts, total products and cart total
     * Shipping is set to 0 if there is a free_shipping cart rule
     *
     * Otherwise,
     * There is one line per product type, any gift products will be included in the quantity of charged products, but the price of gift products will appear as a discount
     * Shipping has its original price, and if it's free, the shipping value will be added as a discount
     *
     * @param bool $hideDiscounts
     *
     * @return GetCartForOrderCreation
     */
    public function setHideDiscounts(bool $hideDiscounts): self
    {
        $this->hideDiscounts = $hideDiscounts;

        return $this;
    }
}
