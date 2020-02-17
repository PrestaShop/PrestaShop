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
     * @param bool $returnedProducts
     *
     * @throws CancelProductFromOrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function updateRefundData(Order $order, OrderRefundSummary $orderRefundSummary, bool $returnedProducts)
    {
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
