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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Cart;
use CartRule;
use Configuration;
use Context;
use Customization;
use Hook;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotEditDeliveredOrderProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\UpdateProductInOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use Product;
use StockAvailable;
use Validate;

/**
 * @internal
 */
final class UpdateProductInOrderHandler extends AbstractOrderHandler implements UpdateProductInOrderHandlerInterface
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
     * {@inheritdoc}
     */
    public function handle(UpdateProductInOrderCommand $command)
    {
        // Return value
        $res = true;

        $order = $this->getOrderObject($command->getOrderId());
        $orderDetail = new OrderDetail($command->getOrderDetailId());
        $cart = new Cart($order->id_cart);
        $orderInvoice = null;
        if (!empty($command->getOrderInvoiceId())) {
            $orderInvoice = new OrderInvoice($command->getOrderInvoiceId());
        }

        // Check fields validity
        $this->assertProductCanBeUpdated($command, $orderDetail, $order, $orderInvoice);

        if (0 < $orderDetail->id_customization) {
            $customization = new Customization($orderDetail->id_customization);
            $customization->quantity = $command->getQuantity();
            $customization->save();
        }
        $product_quantity = $command->getQuantity();

        // @todo: use https://github.com/PrestaShop/decimal for price computations
        $product_price_tax_incl = (float) $command->getPriceTaxIncluded()->round(2);
        $product_price_tax_excl = (float) $command->getPriceTaxExcluded()->round(2);
        $total_products_tax_incl = $product_price_tax_incl * $product_quantity;
        $total_products_tax_excl = $product_price_tax_excl * $product_quantity;

        // Calculate differences of price (Before / After)
        $diff_price_tax_incl = $total_products_tax_incl - $orderDetail->total_price_tax_incl;
        $diff_price_tax_excl = $total_products_tax_excl - $orderDetail->total_price_tax_excl;

        // Apply change on OrderInvoice
        if (isset($orderInvoice)) {
            // If OrderInvoice to use is different, we update the old invoice and new invoice
            if ($orderDetail->id_order_invoice != $orderInvoice->id) {
                $old_order_invoice = new OrderInvoice($orderDetail->id_order_invoice);
                // We remove cost of products
                $old_order_invoice->total_products -= $orderDetail->total_price_tax_excl;
                $old_order_invoice->total_products_wt -= $orderDetail->total_price_tax_incl;

                $old_order_invoice->total_paid_tax_excl -= $orderDetail->total_price_tax_excl;
                $old_order_invoice->total_paid_tax_incl -= $orderDetail->total_price_tax_incl;

                $res &= $old_order_invoice->update();

                $orderInvoice->total_products += $orderDetail->total_price_tax_excl;
                $orderInvoice->total_products_wt += $orderDetail->total_price_tax_incl;

                $orderInvoice->total_paid_tax_excl += $orderDetail->total_price_tax_excl;
                $orderInvoice->total_paid_tax_incl += $orderDetail->total_price_tax_incl;

                $orderDetail->id_order_invoice = $orderInvoice->id;
            }
        }

        if ($diff_price_tax_incl != 0 && $diff_price_tax_excl != 0) {
            $orderDetail->unit_price_tax_excl = $product_price_tax_excl;
            $orderDetail->unit_price_tax_incl = $product_price_tax_incl;

            $orderDetail->total_price_tax_incl += $diff_price_tax_incl;
            $orderDetail->total_price_tax_excl += $diff_price_tax_excl;

            if (isset($orderInvoice)) {
                // Apply changes on OrderInvoice
                $orderInvoice->total_products += $diff_price_tax_excl;
                $orderInvoice->total_products_wt += $diff_price_tax_incl;

                $orderInvoice->total_paid_tax_excl += $diff_price_tax_excl;
                $orderInvoice->total_paid_tax_incl += $diff_price_tax_incl;
            }

            // Apply changes on Order
            $order = new Order($orderDetail->id_order);
            $order->total_products += $diff_price_tax_excl;
            $order->total_products_wt += $diff_price_tax_incl;

            $order->total_paid += $diff_price_tax_incl;
            $order->total_paid_tax_excl += $diff_price_tax_excl;
            $order->total_paid_tax_incl += $diff_price_tax_incl;

            $res &= $order->update();
        }

        $old_quantity = $orderDetail->product_quantity;

        $orderDetail->product_quantity = $product_quantity;
        $orderDetail->reduction_percent = 0;

        // update taxes
        $res &= $orderDetail->updateTaxAmount($order);

        // Save order detail
        $res &= $orderDetail->update();

        // Apply the changes on the cart
        $cart->updateQty(
            abs($old_quantity - $orderDetail->product_quantity),
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            false,
            $orderDetail->product_quantity > $old_quantity ? 'up' : 'down'
        );
        $this->updateCartRules($cart, $order, $orderInvoice);

        // Update weight SUM
        $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float) $order->getTotalWeight();
            $res &= $order_carrier->update();
            if ($res) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        // Save order invoice
        if (isset($orderInvoice)) {
            $res &= $orderInvoice->update();
        }

        // Update product available quantity
        StockAvailable::updateQuantity(
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            $old_quantity - $orderDetail->product_quantity,
            $order->id_shop
        );

        $product = new Product($orderDetail->product_id);

        if (!$product->is_virtual) {
            $order = $order->refreshShippingCost();
        }

        if (!$res) {
            throw new OrderException('An error occurred while editing the product line.');
        }

        Hook::exec('actionOrderEdited', ['order' => $order]);
    }

    /**
     * @param UpdateProductInOrderCommand $command
     * @param OrderDetail $orderDetail
     * @param Order $order
     * @param OrderInvoice|null $orderInvoice
     *
     * @throws OrderException
     */
    private function assertProductCanBeUpdated(
        UpdateProductInOrderCommand $command,
        OrderDetail $orderDetail,
        Order $order,
        OrderInvoice $orderInvoice = null
    ) {
        if (!Validate::isLoadedObject($orderDetail)) {
            throw new OrderException('The Order Detail object could not be loaded.');
        }

        if (null !== $orderInvoice && !Validate::isLoadedObject($orderInvoice)) {
            throw new OrderException('The invoice object cannot be loaded.');
        }

        if (!Validate::isLoadedObject($order)) {
            throw new OrderException('The order object cannot be loaded.');
        }

        if ($orderDetail->id_order != $order->id) {
            throw new OrderException('You cannot edit the order detail for this order.');
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            throw new CannotEditDeliveredOrderProductException('You cannot edit a delivered order.');
        }

        if (null !== $orderInvoice && $orderInvoice->id_order != $order->id) {
            throw new OrderException('You cannot use this invoice for the order');
        }

        if ($command->getPriceTaxIncluded()->isNegative() || $command->getPriceTaxExcluded()->isNegative()) {
            throw new OrderException('Invalid price');
        }

        if (!is_array($command->getQuantity())
            && !Validate::isUnsignedInt($command->getQuantity())
        ) {
            throw new OrderException('Invalid quantity');
        }

        // @todo: check if quantity can be array
//        if (is_array($command->getQuantity())) {
//            foreach ($command->getQuantity() as $qty) {
//                if (!Validate::isUnsignedInt($qty)) {
//                    throw new OrderException('Invalid quantity');
//                }
//            }
//        }

        //check if product is available in stock
        if (!\Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($orderDetail->product_id))) {
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct($orderDetail->product_id, $orderDetail->product_attribute_id);

            if ($availableQuantity < $command->getQuantity()) {
                throw new ProductOutOfStockException('Not enough products in stock');
            }
        }
    }

    /**
     * @param Cart $cart
     * @param Order $order
     * @param OrderInvoice|null $invoice
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateCartRules(Cart $cart, Order $order, ?OrderInvoice $invoice = null)
    {
        $computingPrecision = Context::getContext()->getComputingPrecision();

        $cart = $this->syncCartOrderCartRules($order, $cart);
        Context::getContext()->cart = $cart;
        CartRule::autoAddToCart();
        CartRule::autoRemoveFromCart();

        $orderCartRulesData = $order->getCartRules();
        foreach ($orderCartRulesData as $orderCartRuleData) {
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

        $order = $this->orderAmountUpdater->update($order, $cart, $invoice && !empty($invoice->id));
        $order->update();
    }

    /**
     * @param Order $order
     * @param Cart $cart
     *
     * @return Cart
     */
    private function syncCartOrderCartRules(Order $order, Cart $cart): Cart
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

        sort($orderCartRulesIds);
        sort($cartRulesIds);

        $difference = array_diff($orderCartRulesIds, $cartRulesIds);

        if (count($orderCartRulesIds) > count($cartRulesIds)) {
            foreach ($difference as $cartRuleId) {
                $cart->addCartRule($cartRuleId);
            }
        } else {
            foreach ($difference as $cartRuleId) {
                $cartRule = $cartRules[$cartRuleId];
                $order->addCartRule(
                    $cartRuleId,
                    $cartRule->name[$order->id_lang],
                    [
                        'tax_incl' => $cartRule['value_real'],
                        'tax_excl' => $cartRule['value_tax_exc'],
                    ]
                );
            }
        }

        return $cart;
    }
}
