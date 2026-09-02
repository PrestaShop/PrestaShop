<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Sends email to customer with link for processing the order from cart
 */
class SendProcessOrderEmailCommand
{
    /**
     * @var CartId
     */
    private $cartId;

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
}
