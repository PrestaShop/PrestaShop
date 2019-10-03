<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Configuration;
use Hook;
use Order;
use OrderCarrier;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotEditDeliveredOrderProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\UpdateProductInOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use StockAvailable;
use Tools;
use Validate;

/**
 * @internal
 */
final class UpdateProductInOrderHandler extends AbstractOrderHandler implements UpdateProductInOrderHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductInOrderCommand $command)
    {
        // Return value
        $res = true;

        $order = $this->getOrderObject($command->getOrderId());
        $orderDetail = new OrderDetail($command->getOrderDetailId());
        $orderInvoice = null;

        if (null !== $command->getOrderInvoiceId()) {
            $orderInvoice = new OrderInvoice($command->getOrderInvoiceId());
        }

        // Check fields validity
        $this->assertProductCanBeUpdated($command, $orderDetail, $order, $orderInvoice);

        // @todo: check if quantity can be array
        // If multiple product_quantity, the order details concern a product customized
//        $product_quantity = 0;
//        if (is_array(Tools::getValue('product_quantity'))) {
//            foreach (Tools::getValue('product_quantity') as $id_customization => $qty) {
//                // Update quantity of each customization
//                Db::getInstance()->update('customization', array('quantity' => (int)$qty), 'id_customization = ' . (int)$id_customization);
//                // Calculate the real quantity of the product
//                $product_quantity += $qty;
//            }
//        }

        $product_quantity = $command->getQuantity();

        // @todo: use https://github.com/PrestaShop/decimal for price computations
        $product_price_tax_incl = Tools::ps_round(Tools::getValue('product_price_tax_incl'), 2);
        $product_price_tax_excl = Tools::ps_round(Tools::getValue('product_price_tax_excl'), 2);
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

        $order = $order->refreshShippingCost();

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

        // Clean price

        // @todo: make sure clean
        $product_price_tax_incl = str_replace(',', '.', $command->getPriceTaxIncluded());
        $product_price_tax_excl = str_replace(',', '.', $command->getPriceTaxExcluded());

        if (!Validate::isPrice($product_price_tax_incl) || !Validate::isPrice($product_price_tax_excl)) {
            throw new OrderException('Invalid price');
        }

        if (!is_array($command->getQuantity())
            && !Validate::isUnsignedInt(Tools::getValue('product_quantity'))
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
    }
}
