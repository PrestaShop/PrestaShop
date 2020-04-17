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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

use Cart;
use Currency;
use Order;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use Tools;

class OrderAmountUpdater
{
    /**
     * @param Order $order
     * @param Cart $cart
     * @param bool $hasInvoice
     */
    public function update(
        Order $order,
        Cart $cart,
        bool $hasInvoice
    ): Order {
        // @todo: use https://github.com/PrestaShop/decimal for price computations

        $currency = new Currency((int) $cart->id_currency);
        $computingPrecision = new ComputingPrecision();
        $precision = $computingPrecision->getPrecision((int) $currency->precision);

        $totalMethod = $hasInvoice ? Cart::BOTH_WITHOUT_SHIPPING : Cart::BOTH;

        $orderProducts = $order->getCartProducts();

        $order->total_discounts = (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $orderProducts));
        $order->total_discounts_tax_excl = (float) abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS, $orderProducts));
        $order->total_discounts_tax_incl = (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $orderProducts));

        $order->total_paid = Tools::ps_round(
            (float) $cart->getOrderTotal(true, $totalMethod, $orderProducts),
            $precision
        );
        $order->total_paid_tax_excl = Tools::ps_round(
            (float) $cart->getOrderTotal(false, $totalMethod, $orderProducts),
            $precision
        );
        $order->total_paid_tax_incl = Tools::ps_round(
            (float) $cart->getOrderTotal(true, $totalMethod, $orderProducts),
            $precision
        );

        $order->total_products = (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $orderProducts);
        $order->total_products_wt = (float) $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, $orderProducts);

        $order->total_shipping = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING, $orderProducts);
        $order->total_shipping_tax_excl = $cart->getOrderTotal(false, Cart::ONLY_SHIPPING, $orderProducts);
        $order->total_shipping_tax_incl = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING, $orderProducts);

        $order->total_wrapping = abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $orderProducts));
        $order->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING, $orderProducts));
        $order->total_wrapping_tax_incl = abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $orderProducts));

        return $order;
    }
}
