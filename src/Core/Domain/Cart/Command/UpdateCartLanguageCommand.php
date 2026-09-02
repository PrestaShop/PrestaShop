<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Updates language for given cart
 */
class UpdateCartLanguageCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var LanguageId
     */
    private $newLanguageId;

    /**
     * @param int $cartId
     * @param int $newLanguageId
     */
    public function __construct($cartId, $newLanguageId)
    {
        $this->cartId = new CartId($cartId);
        $this->newLanguageId = new LanguageId($newLanguageId);
    }

    /**
     * @return CartId
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return LanguageId
     */
    public function getNewLanguageId()
    {
        return $this->newLanguageId;
    }
}
