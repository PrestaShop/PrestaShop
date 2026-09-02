<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;

/**
 * Adds cart rule to given cart.
 */
class AddCartRuleToCartCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var CartRuleId
     */
    private $cartRuleId;

    /**
     * @param int $cartId
     * @param int $cartRuleId
     */
    public function __construct($cartId, $cartRuleId)
    {
        $this->cartId = new CartId($cartId);
        $this->cartRuleId = new CartRuleId($cartRuleId);
    }

    /**
     * @return CartId
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return CartRuleId
     */
    public function getCartRuleId()
    {
        return $this->cartRuleId;
    }
}
