<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Query;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Gets only the displayed total of a cart, without building the full cart view. Used by the carts
 * grids, which need the total for every row but none of the rest of the cart view.
 */
class GetCartTotalForViewing
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @param int $cartId
     */
    public function __construct($cartId)
    {
        $this->cartId = new CartId($cartId);
    }

    /**
     * @return CartId
     */
    public function getCartId()
    {
        return $this->cartId;
    }
}
