<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Query;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

class GetCartShopId
{
    private readonly CartId $cartId;

    public function __construct(
        int $cartId,
    ) {
        $this->cartId = new CartId($cartId);
    }

    public function getCartId(): CartId
    {
        return $this->cartId;
    }
}
