<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Query;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Get cart for viewing in Back Office
 */
class GetCartForViewing
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
