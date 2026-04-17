<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;

/**
 * Cart identity
 */
class CartId
{
    /**
     * @var int
     */
    private $cartId;

    /**
     * @param int $cartId
     *
     * @throws CartConstraintException if cart id is not valid
     */
    public function __construct($cartId)
    {
        if (!is_int($cartId) || 0 >= $cartId) {
            throw new CartConstraintException(sprintf('Cart id must be integer greater than zero, but %s given.', var_export($cartId, true)));
        }

        $this->cartId = $cartId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->cartId;
    }
}
