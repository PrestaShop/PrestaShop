<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Updates cart carrier (a.k.a delivery option) with new one.
 */
class UpdateCartCarrierCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var int
     */
    private $newCarrierId;

    /**
     * @param int $cartId
     * @param int $newCarrierId
     *
     * @throws CartConstraintException
     */
    public function __construct($cartId, $newCarrierId)
    {
        $this->cartId = new CartId($cartId);
        $this->newCarrierId = $newCarrierId;
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return int
     */
    public function getNewCarrierId(): int
    {
        return $this->newCarrierId;
    }
}
