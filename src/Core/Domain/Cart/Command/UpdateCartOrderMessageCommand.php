<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Stores the order message of a cart that has no order yet, so that a draft written while an order
 * is being prepared in the back office survives leaving the page.
 */
class UpdateCartOrderMessageCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var string
     */
    private $orderMessage;

    public function __construct(int $cartId, string $orderMessage)
    {
        $this->cartId = new CartId($cartId);
        $this->orderMessage = $orderMessage;
    }

    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    public function getOrderMessage(): string
    {
        return $this->orderMessage;
    }
}
