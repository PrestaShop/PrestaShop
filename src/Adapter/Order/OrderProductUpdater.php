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

use Address;
use Cart;
use CartRule;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Language;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use Product;
use StockAvailable;
use Tools;
use Validate;

/**
 * Increase or decrease quantity of an order's product.
 * Recalculate cart rules, order's prices and shipping infos.
 */
class OrderProductUpdater
{
    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;

    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    public function __construct(OrderAmountUpdater $orderAmountUpdater, ContextStateManager $contextStateManager)
    {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param int $oldQuantity
     * @param int $newQuantity
     * @param bool $hasInvoice
     *
     * @return Order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function update(
        Order $order,
        OrderDetail $orderDetail,
        int $oldQuantity,
        int $newQuantity,
        bool $hasInvoice = false
    ): Order {
        $cart = new Cart($order->id_cart);

        $this->contextStateManager
            ->setCart($cart)
            ->setCurrency(new Currency($cart->id_currency))
            ->setCustomer(new Customer($cart->id_customer))
            ->setLanguage(new Language($cart->id_lang))
            ->setCountry($this->getTaxCountry($cart))
        ;

        try {
            // Update quantity on the cart and stock
            $cart = $this->updateCartProductQuantity($cart, $orderDetail, $oldQuantity, $newQuantity);

            // Fix differences between cart's cartRules and order's cartRules
            $cart = $this->syncCartAndOrderCartRules($cart, $order);

            // Recalculate amounts of cartRules
            $this->recalculateCartRules($cart, $order);

            // Update prices on the order
            $order = $this->updateOrderAmounts($cart, $order, $hasInvoice);

            // Update weight and shipping infos
            $order = $this->updateOrderShippingInfos($order, new Product((int) $orderDetail->product_id));
        } finally {
            $this->contextStateManager->restoreContext();
        }

        return $order;
    }

    /**
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     * @param int $oldQuantity
     * @param int $newQuantity
     *
     * @return Cart
     */
    private function updateCartProductQuantity(
        Cart $cart,
        OrderDetail $orderDetail,
        int $oldQuantity,
        int $newQuantity
    ): Cart {
        $deltaQuantity = $oldQuantity - $newQuantity;

        if (0 !== $deltaQuantity) {
            $updateQuantityResult = $cart->updateQty(
                abs($deltaQuantity),
                $orderDetail->product_id,
                $orderDetail->product_attribute_id,
                false,
                $deltaQuantity > 0 ? 'down' : 'up'
            );

            if (-1 === $updateQuantityResult) {
                throw new \LogicException('Minimum quantity is not respected');
            } elseif (true !== $updateQuantityResult) {
                throw new \LogicException('Something went wrong');
            }

            // Update product available quantity
            StockAvailable::updateQuantity(
                $orderDetail->product_id,
                $orderDetail->product_attribute_id,
                $deltaQuantity,
                $cart->id_shop
            );
        }

        return $cart;
    }

    /**
     * @param Cart $cart
     * @param Order $order
     *
     * @return Cart
     */
    private function syncCartAndOrderCartRules(Cart $cart, Order $order): Cart
    {
        $orderCartRulesData = $order->getCartRules();
        $orderCartRulesIds = array_map(
            function (array $orderCartRuleData) { return (int) $orderCartRuleData['id_cart_rule']; },
            $orderCartRulesData
        );

        $cartRules = $cart->getCartRules();
        $cartRulesIds = array_map(
            function (array $cartRule) { return (int) $cartRule['id_cart_rule']; },
            $cartRules
        );

        if (count($orderCartRulesIds) === count($cartRulesIds)) {
            return $cart;
        }

        if (count($orderCartRulesIds) > count($cartRulesIds)) {
            sort($orderCartRulesIds);
            sort($cartRulesIds);

            foreach (array_diff($orderCartRulesIds, $cartRulesIds) as $cartRuleId) {
                $cart->addCartRule($cartRuleId);
            }
        } else {
            throw new \LogicException('Cart has more cart rules than order. This should never happen.');
        }

        return $cart;
    }

    /**
     * @param Cart $cart
     * @param Order $order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function recalculateCartRules(Cart $cart, Order $order): void
    {
        $computingPrecision = $this->getPrecisionFromCart($cart);
        Context::getContext()->cart = $cart;
        CartRule::autoAddToCart();
        CartRule::autoRemoveFromCart();

        foreach ($order->getCartRules() as $orderCartRuleData) {
            $orderCartRule = new OrderCartRule((int) $orderCartRuleData['id_order_cart_rule']);
            $idCartRule = (int) $orderCartRule->id_cart_rule;
            $cartRule = new CartRule($idCartRule);
            $values = [
                'tax_incl' => Tools::ps_round($cartRule->getContextualValue(true), $computingPrecision),
                'tax_excl' => Tools::ps_round($cartRule->getContextualValue(false), $computingPrecision),
            ];

            if (
                ($values['tax_incl'] !== Tools::ps_round($orderCartRule->value, $computingPrecision)) ||
                ($values['tax_excl'] !== Tools::ps_round($orderCartRule->value_tax_excl, $computingPrecision))
            ) {
                $orderCartRule->value = $values['tax_incl'];
                $orderCartRule->value_tax_excl = $values['tax_excl'];

                $orderCartRule->update();
            }
        }
    }

    /**
     * @param Cart $cart
     * @param Order $order
     * @param bool $hasInvoice
     *
     * @return Order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderAmounts(Cart $cart, Order $order, bool $hasInvoice): Order
    {
        $order = $this->orderAmountUpdater->update($order, $cart, $hasInvoice);
        $order->update();

        return $order;
    }

    /**
     * @param Order $order
     * @param Product $product
     *
     * @return Order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderShippingInfos(Order $order, Product $product): Order
    {
        $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());

        if (Validate::isLoadedObject($orderCarrier)) {
            $orderCarrier->weight = (float) $order->getTotalWeight();

            if ($orderCarrier->update()) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $orderCarrier->weight);
            }
        }

        if (!$product->is_virtual) {
            $order = $order->refreshShippingCost();
        }

        return $order;
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

    /**
     * @param Cart $cart
     *
     * @return Country
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function getTaxCountry(Cart $cart): Country
    {
        $taxAddressType = Configuration::get('PS_TAX_ADDRESS_TYPE');
        $taxAddressId = property_exists($cart, $taxAddressType) ? $cart->{$taxAddressType} : $cart->id_address_delivery;
        $taxAddress = new Address($taxAddressId);

        return new Country($taxAddress->id_country);
    }
}
