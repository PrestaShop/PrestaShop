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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Configuration;
use Customization;
use Hook;
use Order;
use OrderCarrier;
use OrderDetail;
use OrderInvoice;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\Invoice\DTO\InvoiceTotalNumbers;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\DTO\OrderTotalNumbers;
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
     * {@inheritdoc}
     */
    public function handle(UpdateProductInOrderCommand $command)
    {
        // Return value
        $res = true;

        $order = $this->getOrderObject($command->getOrderId());
        $orderDetail = new OrderDetail($command->getOrderDetailId());
        $invoice = null;
        if (!empty($command->getOrderInvoiceId())) {
            $invoice = new OrderInvoice($command->getOrderInvoiceId());
        }

        // Check fields validity
        $this->assertProductCanBeUpdated($command, $orderDetail, $order, $invoice);

        if (0 < $orderDetail->id_customization) {
            $customization = new Customization($orderDetail->id_customization);
            $customization->quantity = $command->getQuantity();
            $customization->save();
        }
        $product_quantity = $command->getQuantity();

        $productPriceTaxIncl = $this->number($command->getPriceTaxIncluded());
        $productPriceTaxExcl = $this->number($command->getPriceTaxExcluded());
        $productQty = $this->number($product_quantity);

        $totalProductsTaxIncl = $productPriceTaxIncl->times($productQty);
        $totalProductsTaxExcl = $productPriceTaxExcl->times($productQty);

        // Calculate differences of price (Before / After)
        $diffPriceTaxIncl = $totalProductsTaxIncl->minus($this->number($orderDetail->total_price_tax_incl));
        $diffPriceTaxExcl = $totalProductsTaxExcl->minus($this->number($orderDetail->total_price_tax_excl));

        // Apply change on OrderInvoice
        if (isset($invoice)) {
            // If OrderInvoice to use is different, we update the old invoice and new invoice
            if ($orderDetail->id_order_invoice != $invoice->id) {
                $oldInvoice = new OrderInvoice($orderDetail->id_order_invoice);

                // We remove cost of products
                $this->decrementInvoiceTotals(
                    $oldInvoice,
                    $this->number($orderDetail->total_price_tax_excl),
                    $this->number($orderDetail->total_price_tax_incl)
                );

                $res &= $oldInvoice->update();

                $this->incrementInvoiceTotals(
                    $invoice,
                    $this->number($orderDetail->total_price_tax_excl),
                    $this->number($orderDetail->total_price_tax_incl)
                );

                $orderDetail->id_order_invoice = $invoice->id;
            }
        }

        $zero = new Number('0');
        if (!$diffPriceTaxIncl->equals($zero) && !$diffPriceTaxExcl->equals($zero)) {
            $orderDetail->unit_price_tax_excl = (float) (string) $productPriceTaxExcl;
            $orderDetail->unit_price_tax_incl = (float) (string) $productPriceTaxIncl;
            $orderDetail->total_price_tax_incl = (float) (string) $this->number($orderDetail->total_price_tax_incl)
                ->plus($diffPriceTaxIncl)
            ;
            $orderDetail->total_price_tax_excl = (float) (string) $this->number($orderDetail->total_price_tax_excl)
                ->plus($diffPriceTaxExcl)
            ;

            if (isset($invoice)) {
                // Apply changes on OrderInvoice
                $this->incrementInvoiceTotals(
                    $invoice,
                    $diffPriceTaxExcl,
                    $diffPriceTaxIncl
                );
            }

            // Apply changes on Order
            $order = new Order($orderDetail->id_order);
            $orderTotals = OrderTotalNumbers::buildFromOrder($order);

            $order->total_products = (float) (string) $orderTotals->getTotalProducts()->plus($diffPriceTaxExcl);
            $order->total_products_wt = (float) (string) $orderTotals->getTotalProductsWt()->plus($diffPriceTaxIncl);
            $order->total_paid = (float) (string) $orderTotals->getTotalPaid()->plus($diffPriceTaxIncl);
            $order->total_paid_tax_excl = (float) (string) $orderTotals->getTotalPaidTaxExcl()->plus($diffPriceTaxExcl);
            $order->total_paid_tax_incl = (float) (string) $orderTotals->getTotalPaidTaxIncl()->plus($diffPriceTaxIncl);

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
        if (isset($invoice)) {
            $res &= $invoice->update();
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

        $priceIsValid = Validate::isPrice((string) $command->getPriceTaxIncluded()) && Validate::isPrice((string) $command->getPriceTaxExcluded());

        if (!$priceIsValid) {
            throw new OrderException('Invalid price');
        }

        if (!is_array($command->getQuantity())
            && !Validate::isUnsignedInt($command->getQuantity())
        ) {
            throw new OrderException('Invalid quantity');
        }

        //check if product is available in stock
        if (!\Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($orderDetail->product_id))) {
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct($orderDetail->product_id, $orderDetail->product_attribute_id);

            if ($availableQuantity < $command->getQuantity()) {
                throw new ProductOutOfStockException('Not enough products in stock');
            }
        }
    }

    /**
     * @param OrderInvoice $invoice
     * @param Number $priceTaxExcl
     * @param Number $priceTaxIncl
     */
    private function incrementInvoiceTotals(
        OrderInvoice $invoice,
        Number $priceTaxExcl,
        Number $priceTaxIncl
    ): void {
        $invoiceTotals = InvoiceTotalNumbers::buildFromInvoice($invoice);

        $invoice->total_products = (float) (string) $invoiceTotals->getTotalProducts()->plus($priceTaxExcl);
        $invoice->total_products_wt = (float) (string) $invoiceTotals->getTotalProductsWt()->plus($priceTaxIncl);
        $invoice->total_paid_tax_excl = (float) (string) $invoiceTotals->getTotalPaidTaxExcl()->plus($priceTaxExcl);
        $invoice->total_paid_tax_incl = (float) (string) $invoiceTotals->getTotalPaidTaxIncl()->plus($priceTaxIncl);
    }

    /**
     * @param OrderInvoice $invoice
     * @param Number $priceTaxExcl
     * @param Number $priceTaxIncl
     */
    private function decrementInvoiceTotals(
        OrderInvoice $invoice,
        Number $priceTaxExcl,
        Number $priceTaxIncl
    ): void {
        $invoiceTotals = InvoiceTotalNumbers::buildFromInvoice($invoice);

        $invoice->total_products = (float) (string) $invoiceTotals->getTotalProducts()->minus($priceTaxExcl);
        $invoice->total_products_wt = (float) (string) $invoiceTotals->getTotalProductsWt()->minus($priceTaxIncl);
        $invoice->total_paid_tax_excl = (float) (string) $invoiceTotals->getTotalPaidTaxExcl()->minus($priceTaxExcl);
        $invoice->total_paid_tax_incl = (float) (string) $invoiceTotals->getTotalPaidTaxIncl()->minus($priceTaxIncl);
    }
}
