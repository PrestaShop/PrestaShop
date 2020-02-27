<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use Customization;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CancelProductFromOrderException;
use PrestaShopDatabaseException;
use PrestaShopException;

class OrderRefundUpdater
{
    /**
     * @param Order $order
     * @param OrderRefundSummary $orderRefundSummary
     * @param bool $returnedProducts
     * @param bool $restock
     *
     * @throws CancelProductFromOrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function updateRefundData(
        Order $order,
        OrderRefundSummary $orderRefundSummary,
        bool $returnedProducts,
        bool $restock
    ) {
        // I wonder it this is really useful since partial refund is supposed to be enabled only once order
        // is paid Maybe this should be a more general check at the beginning of the handler and throw an error
        if (!$order->hasBeenPaid()) {
            return;
        }

        // Update order details (after credit slip to avoid updating refunded quantities while the credit slip fails)
        foreach ($orderRefundSummary->getProductRefunds() as $orderDetailId => $productRefund) {
            $orderDetail = $orderRefundSummary->getOrderDetailById($orderDetailId);
            // There is a distinction between a product returned and refunded (depending if the order was delivered or not)
            if ($returnedProducts) {
                $orderDetail->product_quantity_return += $productRefund['quantity'];
            } else {
                $orderDetail->product_quantity_refunded += $productRefund['quantity'];
            }

            // This was previously done in OrderSlip::create, but it was not consistent and too complicated
            // Besides this now allows to track refunded products even when credit slip is not generated
            $orderDetail->total_refunded_tax_excl += $productRefund['total_refunded_tax_excl'];
            $orderDetail->total_refunded_tax_incl += $productRefund['total_refunded_tax_incl'];

            if ($restock) {
                $reinjectableQuantity = (int) $orderDetail->product_quantity - (int) $orderDetail->product_quantity_reinjected;
                $quantityToReinject = $productRefund['quantity'] > $reinjectableQuantity ? $reinjectableQuantity : $productRefund['quantity'];
                $orderDetail->product_quantity_reinjected += $quantityToReinject;
            }

            if (!$orderDetail->update()) {
                throw new CancelProductFromOrderException('Cannot update order detail');
            }

            // Update customization
            if ($orderDetail->id_customization) {
                $customization = new Customization($orderDetail->id_customization);
                if ($returnedProducts) {
                    $customization->quantity_returned += $productRefund['quantity'];
                } else {
                    $customization->quantity_refunded += $productRefund['quantity'];
                }

                if (!$customization->update()) {
                    throw new CancelProductFromOrderException('Cannot update customization');
                }
            }
        }
    }
}
