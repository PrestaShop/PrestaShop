<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order;

/**
 * Discount types that can be added to an order
 */
final class OrderDiscountType
{
    /**
     * Discount type with percent (%) amount
     */
    public const DISCOUNT_PERCENT = 'percent';

    /**
     * Discount type with money (EUR, USD & etc) amount
     */
    public const DISCOUNT_AMOUNT = 'amount';

    /**
     * Discount type with free shipping
     */
    public const FREE_SHIPPING = 'free_shipping';
}
