<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
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
    const PRODUCT_PRICES_REFUND = 0;

    /**
     * Refund based on product prices, but do not refund the voucher amount
     */
    const PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND = 1;

    /**
     * The refund amount is specified manually
     */
    const SPECIFIC_AMOUNT_REFUND = 2;
}
