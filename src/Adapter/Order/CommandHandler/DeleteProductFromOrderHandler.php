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

use Cart;
use Configuration;
use Hook;
use Order;
use OrderCarrier;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\DTO\OrderTotalNumbers;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\DeleteProductFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\DeleteProductFromOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use Validate;

/**
 * @internal
 */
final class DeleteProductFromOrderHandler extends AbstractOrderCommandHandler implements DeleteProductFromOrderHandlerInterface
{

    /**
     * @var UpdateOrderStatusHandler
     */
    private $updateOrderStatusHandler;

    /**
     * @param UpdateOrderStatusHandler $updateOrderStatusHandler
     */
    public function __construct(
        UpdateOrderStatusHandler $updateOrderStatusHandler
    ) {
        $this->updateOrderStatusHandler = $updateOrderStatusHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteProductFromOrderCommand $command)
    {
        $orderDetail = new OrderDetail($command->getOrderDetailId());
        $order = $this->getOrderObject($command->getOrderId());
        $this->assertProductCanBeDeleted($order, $orderDetail);
        $invoiceId = (int) $orderDetail->id_order_invoice;

        if (!$this->updateOrder($order, $orderDetail)) {
            throw new OrderException('An error occurred while attempting to delete product from order.');
        }
        if (!$this->updateOrderWeight($order)) {
            throw new OrderException('An error occurred while updating order weight after product deletion from order.');
        }

        $this->reinjectQuantity($orderDetail, $orderDetail->product_quantity, true);
        $order->refreshShippingCost();

        if ($invoiceId != 0 && !$this->updateOrderInvoice($order, $invoiceId)) {
            throw new OrderException('An error occurred while updating order invoice after product deletion from order.');
        }

        Hook::exec('actionOrderEdited', ['order' => $order]);
    }

    /**
     * @param Order $order
     * @param int $invoiceId
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderInvoice(Order $order, int $invoiceId): bool
    {
        $orderInvoice = new OrderInvoice($invoiceId);

        $orderInvoice->total_paid_tax_excl = $order->total_paid_tax_excl;
        $orderInvoice->total_paid_tax_incl = $order->total_paid_tax_incl;
        $orderInvoice->total_products = $order->total_products;
        $orderInvoice->total_products_wt = $order->total_products_wt;
        $orderInvoice->total_shipping_tax_excl = $order->total_shipping_tax_excl;
        $orderInvoice->total_shipping_tax_incl = $order->total_shipping_tax_incl;

        return $orderInvoice->update();
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     *
     * @return bool
     */
    private function updateOrder(Order $order, OrderDetail $orderDetail)
    {
        $orderTotals = OrderTotalNumbers::buildFromOrder($order);

        $order->total_paid = (float) (string) $orderTotals->getTotalPaid()
            ->minus($this->number($orderDetail->total_price_tax_incl))
        ;
        $order->total_paid_tax_incl = (float) (string) $orderTotals->getTotalPaidTaxIncl()
            ->minus($this->number($orderDetail->total_price_tax_incl))
        ;
        $order->total_paid_tax_excl = (float) (string) $orderTotals->getTotalPaidTaxExcl()
            ->minus($this->number($orderDetail->total_price_tax_excl))
        ;
        $order->total_products = (float) (string) $orderTotals->getTotalProducts()
            ->minus($this->number($orderDetail->total_price_tax_excl))
        ;
        $order->total_products_wt = (float) (string) $orderTotals->getTotalProductsWt()
            ->minus($this->number($orderDetail->total_price_tax_incl))
        ;

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
