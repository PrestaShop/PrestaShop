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
use Currency;
use Customer;
use Exception;
use Hook;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\DeleteProductFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\DeleteProductFromOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use Tools;
use Validate;

/**
 * @internal
 */
final class DeleteProductFromOrderHandler extends AbstractOrderCommandHandler implements DeleteProductFromOrderHandlerInterface
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;

    /**
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(ContextStateManager $contextStateManager, OrderAmountUpdater $orderAmountUpdater)
    {
        $this->contextStateManager = $contextStateManager;
        $this->orderAmountUpdater = $orderAmountUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteProductFromOrderCommand $command)
    {
        $orderDetail = new OrderDetail($command->getOrderDetailId());
        $order = new Order($command->getOrderId()->getValue());

        $this->assertProductCanBeDeleted($order, $orderDetail);

        $cart = new Cart($order->id_cart);

        $this->contextStateManager
            ->setCart($cart)
            ->setCurrency(new Currency($order->id_currency))
            ->setCustomer(new Customer($order->id_customer));

        try {
            $result = true;
            $result &= $this->updateCart($orderDetail, $cart);
            $result &= $this->updateOrderCartRules($order, $cart);
            $result &= $this->updateOrderInvoice($orderDetail);

            // Reinject quantity in stock
            $this->reinjectQuantity($orderDetail, $orderDetail->product_quantity, true);

            $result &= $this->updateOrder($order, $orderDetail, $cart);
            $result &= $this->updateOrderWeight($order);

            if (!$result) {
                throw new OrderException('An error occurred while attempting to delete product from order.');
            }

            $order = $order->refreshShippingCost();

            Hook::exec('actionOrderEdited', ['order' => $order]);
        } catch (Exception $e) {
            $this->contextStateManager->restoreContext();
            throw $e;
        }

        $this->contextStateManager->restoreContext();
    }

    /**
     * @param OrderDetail $orderDetail
     * @param Cart $cart
     *
     * @return bool
     */
    private function updateCart(OrderDetail $orderDetail, Cart &$cart)
    {
        // Update Product Quantity in the cart
        $cart->updateQty(
            $orderDetail->product_quantity,
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            false,
            'down'
        );
        // Remove & Add CartRules applied to cart
        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();

        return $cart->update();
    }

    /**
     * Remove previous cart rules applied to order
     *
     * @param Order $order
     * @param Cart $cart
     *
     * @return bool
     */
    private function updateOrderCartRules(Order $order, Cart $cart)
    {
        $computingPrecision = $this->getPrecisionFromCart($cart);
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
                return false;
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

        return true;
    }

    /**
     * @param OrderDetail $orderDetail
     *
     * @return bool
     */
    private function updateOrderInvoice(OrderDetail $orderDetail)
    {
        if ($orderDetail->id_order_invoice != 0) {
            $order_invoice = new OrderInvoice($orderDetail->id_order_invoice);
            // @todo: use https://github.com/PrestaShop/decimal for price computations
            $order_invoice->total_paid_tax_excl -= $orderDetail->total_price_tax_excl;
            $order_invoice->total_paid_tax_incl -= $orderDetail->total_price_tax_incl;
            $order_invoice->total_products -= $orderDetail->total_price_tax_excl;
            $order_invoice->total_products_wt -= $orderDetail->total_price_tax_incl;

            return $order_invoice->update();
        }

        return true;
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param Cart $cart
     *
     * @return bool
     */
    private function updateOrder(Order $order, OrderDetail $orderDetail, Cart $cart)
    {
        $order = $this->orderAmountUpdater->update($order, $cart, $orderDetail->id_order_invoice != 0);

        return $order->update();
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    private function updateOrderWeight(Order $order)
    {
        $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());

        if (Validate::isLoadedObject($orderCarrier)) {
            $orderCarrier->weight = (float) $order->getTotalWeight();
            $updated = $orderCarrier->update();

            if ($updated) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $orderCarrier->weight);
            }

            return $updated;
        }

        return true;
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     */
    private function assertProductCanBeDeleted(Order $order, OrderDetail $orderDetail)
    {
        if (!Validate::isLoadedObject($orderDetail)) {
            throw new OrderException('Order detail could not be found.');
        }

        if (!Validate::isLoadedObject($order)) {
            throw new OrderNotFoundException(new OrderId((int) $order->id), 'Order could not be found.');
        }

        if ($orderDetail->id_order != $order->id) {
            throw new OrderException('Order detail does not belong to order.');
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            throw new OrderException('Delivered order cannot be modified.');
        }
    }
}
