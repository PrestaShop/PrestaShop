<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param OrderRefundSummary $orderRefundSummary
     * @param bool $returnedProducts
     * @param bool $restock
     *
     * @throws CancelProductFromOrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function updateRefundData(
        OrderRefundSummary $orderRefundSummary,
        bool $returnedProducts,
        bool $restock
    ) {
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
