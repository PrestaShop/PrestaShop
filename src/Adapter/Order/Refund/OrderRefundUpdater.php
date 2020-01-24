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
     *
     * @throws PrestaShopException
     */
    public function deleteFromOrder(Order $order, OrderRefundSummary $orderRefundSummary)
    {
        foreach ($orderRefundSummary->getProductRefunds() as $orderDetailId => $productRefund) {
            $orderDetail = $orderRefundSummary->getOrderDetailById($orderDetailId);
            $order->deleteProduct($order, $orderDetail, $productRefund['quantity']);
            if ($orderDetail->id_customization > 0) {
                $order->deleteCustomization($orderDetail->id_customization, $productRefund['quantity'], $orderDetail);
            }
        }
    }

    /**
     * @param Order $order
     * @param OrderRefundSummary $orderRefundSummary
     *
     * @throws CancelProductFromOrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function updateRefundData(Order $order, OrderRefundSummary $orderRefundSummary)
    {
        // I wonder it this is really useful since partial refund is supposed to be enabled only once order
        // is paid Maybe this should be a more general check at the beginning of the handler and throw an error
        if (!$order->hasBeenPaid()) {
            return;
        }

        // Update order details (after credit slip to avoid updating refunded quantities while the credit slip fails)
        foreach ($orderRefundSummary->getProductRefunds() as $orderDetailId => $productRefund) {
            $orderDetail = $orderRefundSummary->getOrderDetailById($orderDetailId);
            // It appears partial refund only manages product_quantity_refunded when Order::deleteProduct
            // makes a distinction between product_quantity_refunded and product_quantity_returned depending
            // on the order status (delivered or not) But this method could not be used as it can fail when
            // merchandising return is disabled
            $orderDetail->product_quantity_refunded += $productRefund['quantity'];

            // This was previously done in OrderSlip::create, but it was not consistent and too complicated
            // Besides this now allows to track refunded products even when credit slip is not generated
            $orderDetail->total_refunded_tax_excl += $productRefund['total_refunded_tax_excl'];
            $orderDetail->total_refunded_tax_incl += $productRefund['total_refunded_tax_incl'];

            if (!$orderDetail->update()) {
                throw new CancelProductFromOrderException('Cannot update order detail');
            }

            // Update customization
            if ($orderDetail->id_customization) {
                $customization = new Customization($orderDetail->id_customization);
                $customization->quantity_refunded += $productRefund['quantity'];

                if (!$customization->update()) {
                    throw new CancelProductFromOrderException('Cannot update customization');
                }
            }
        }
    }
}
