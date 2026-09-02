<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

/**
 * Updates cart currency
 */
class UpdateCartCurrencyCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var CurrencyId
     */
    private $newCurrencyId;

    /**
     * @param int $cartId
     * @param int $newCurrencyId
     */
    public function __construct($cartId, $newCurrencyId)
    {
        $this->cartId = new CartId($cartId);
        $this->newCurrencyId = new CurrencyId($newCurrencyId);
    }

    /**
     * @return CartId
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return CurrencyId
     */
    public function getNewCurrencyId()
    {
        return $this->newCurrencyId;
    }
}
