<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
