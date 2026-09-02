<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;

/**
 * Removes given cart rule from cart.
 */
class RemoveCartRuleFromCartCommand
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
     *
     * @throws CartConstraintException
     */
    public function __construct(int $cartId, int $cartRuleId)
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
