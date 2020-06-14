<?php

namespace PrestaShop\PrestaShop\Adapter\Order;

use Cart;
use CartRule;
use Configuration;
use Context;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
use Product;
use StockAvailable;
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

    public function __construct(OrderAmountUpdater $orderAmountUpdater)
    {
        $this->orderAmountUpdater = $orderAmountUpdater;
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

    private function recalculateCartRules(Cart $cart, Order $order)
    {
        $computingPrecision = Context::getContext()->getComputingPrecision();
        Context::getContext()->cart = $cart;
        CartRule::autoAddToCart();
        CartRule::autoRemoveFromCart();

        foreach ($order->getCartRules() as $orderCartRuleData) {
            $orderCartRule = new OrderCartRule((int) $orderCartRuleData['id_order_cart_rule']);
            $idCartRule = (int) $orderCartRule->id_cart_rule;
            $cartRule = new CartRule($idCartRule);
            $values = [
                'tax_incl' => \Tools::ps_round($cartRule->getContextualValue(true), $computingPrecision),
                'tax_excl' => \Tools::ps_round($cartRule->getContextualValue(false), $computingPrecision),
            ];

            if (
                ($values['tax_incl'] !== \Tools::ps_round($orderCartRule->value, $computingPrecision)) ||
                ($values['tax_excl'] !== \Tools::ps_round($orderCartRule->value_tax_excl, $computingPrecision))
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
}
