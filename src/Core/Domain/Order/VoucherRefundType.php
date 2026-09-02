<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order;

/**
 * When refunding an order that was partially paid with a voucher you have different way
 * to refund it.
 */
class VoucherRefundType
{
    /**
     * Refund based on product prices (the initial voucher amount is ignored)
     */
    public const PRODUCT_PRICES_REFUND = 0;

    /**
     * Refund based on product prices, but do not refund the voucher amount
     */
    public const PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND = 1;

    /**
     * The refund amount is specified manually
     */
    public const SPECIFIC_AMOUNT_REFUND = 2;
}
