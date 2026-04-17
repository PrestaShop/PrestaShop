<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject;

class DiscountType
{
    public const CART_LEVEL = 'cart_level';
    public const PRODUCT_LEVEL = 'product_level';
    public const FREE_GIFT = 'free_gift';
    public const FREE_SHIPPING = 'free_shipping';
    public const ORDER_LEVEL = 'order_level';

    public function __construct(private readonly string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
