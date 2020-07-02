<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

use Cache;
use Cart;
use CartRule;
use Context;
use Currency;
use Order;
use OrderCartRule;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
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
    ): bool {
        // @todo: use https://github.com/PrestaShop/decimal for price computations
        $computingPrecision = $this->getPrecisionFromCart($cart);

        $totalMethod = $hasInvoice ? Cart::BOTH_WITHOUT_SHIPPING : Cart::BOTH;

        // Recalculate cart rules and Fix differences between cart's cartRules and order's cartRules
        $this->updateOrderCartRules($order, $cart, $computingPrecision);

        $orderProducts = $order->getCartProducts();

        $order->total_discounts = (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $orderProducts));
        $order->total_discounts_tax_excl = (float) abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS, $orderProducts));
        $order->total_discounts_tax_incl = (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $orderProducts));

        $order->total_paid = Tools::ps_round(
            (float) $cart->getOrderTotal(true, $totalMethod, $orderProducts),
            $computingPrecision
        );
        $order->total_paid_tax_excl = Tools::ps_round(
            (float) $cart->getOrderTotal(false, $totalMethod, $orderProducts),
            $computingPrecision
        );
        $order->total_paid_tax_incl = Tools::ps_round(
            (float) $cart->getOrderTotal(true, $totalMethod, $orderProducts),
            $computingPrecision
        );

        $order->total_products = (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $orderProducts);
        $order->total_products_wt = (float) $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, $orderProducts);

        $order->total_shipping = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING, $orderProducts);
        $order->total_shipping_tax_excl = $cart->getOrderTotal(false, Cart::ONLY_SHIPPING, $orderProducts);
        $order->total_shipping_tax_incl = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING, $orderProducts);

        $order->total_wrapping = abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $orderProducts));
        $order->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING, $orderProducts));
        $order->total_wrapping_tax_incl = abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $orderProducts));

        return $order->update();
    }

    /**
     * Remove previous cart rules applied to order
     *
     * @param Order $order
     * @param Cart $cart
     * @param int $computingPrecision
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderCartRules(Order $order, Cart $cart, int $computingPrecision): void
    {
        Context::getContext()->cart = $cart;
        CartRule::resetStaticCache();
        Cache::clean('getContextualValue_*');
        CartRule::autoAddToCart();
        CartRule::autoRemoveFromCart();

        $newCartRules = $cart->getCartRules();
        foreach ($order->getCartRules() as $orderCartRuleData) {
            foreach ($newCartRules as $newCartRule) {
                if ($newCartRule['id_cart_rule'] == $orderCartRuleData['id_cart_rule']) {
                    // Cart rule is still in the cart no need to remove it, but we update it as the amount may have changed
                    $cartRule = new CartRule($newCartRule['id_cart_rule']);

                    $orderCartRule = new OrderCartRule($orderCartRuleData['id_order_cart_rule']);
                    $orderCartRule->id_order = $order->id;
                    $orderCartRule->name = $newCartRule['name'];
                    $orderCartRule->value = Tools::ps_round($cartRule->getContextualValue(true), $computingPrecision);
                    $orderCartRule->value_tax_excl = Tools::ps_round($cartRule->getContextualValue(false), $computingPrecision);
                    $orderCartRule->save();
                    continue 2;
                }
            }

            // This one is no longer in the new cart rules so we delete it
            $orderCartRule = new OrderCartRule($orderCartRuleData['id_order_cart_rule']);
            if (!$orderCartRule->delete()) {
                throw new OrderException('Could not delete order cart rule from database.');
            }
        }

        // Finally add the new cart rules that are not in the Order
        foreach ($newCartRules as $newCartRule) {
            foreach ($order->getCartRules() as $orderCartRuleData) {
                if ($newCartRule['id_cart_rule'] == $orderCartRuleData['id_cart_rule']) {
                    // This cart rule is already present no need to add it
                    continue 2;
                }
            }

            // Add missing order cart rule
            $cartRule = new CartRule($newCartRule['id_cart_rule']);

            $orderCartRule = new OrderCartRule();
            $orderCartRule->id_order = $order->id;
            $orderCartRule->id_cart_rule = $newCartRule['id_cart_rule'];
            $orderCartRule->id_order_invoice = $order->getInvoicesCollection()->getLast();
            $orderCartRule->name = $newCartRule['name'];
            $orderCartRule->value = Tools::ps_round($cartRule->getContextualValue(true), $computingPrecision);
            $orderCartRule->value_tax_excl = Tools::ps_round($cartRule->getContextualValue(false), $computingPrecision);
            $orderCartRule->save();
        }
    }

    /**
     * @param Cart $cart
     *
     * @return int
     */
    private function getPrecisionFromCart(Cart $cart): int
    {
        $computingPrecision = new ComputingPrecision();
        $currency = new Currency((int) $cart->id_currency);

        return $computingPrecision->getPrecision((int) $currency->precision);
    }
}
