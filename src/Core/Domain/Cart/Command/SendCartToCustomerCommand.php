<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Sends email to the customer to process the payment for cart.
 *
 * @deprecated Since 9.0 and will be removed in the next major.
 */
class SendCartToCustomerCommand
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
