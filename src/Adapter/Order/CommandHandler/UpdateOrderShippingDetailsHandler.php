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

use Carrier;
use Customer;
use Hook;
use OrderCarrier;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\UpdateOrderShippingDetailsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
final class UpdateOrderShippingDetailsHandler extends AbstractOrderHandler implements UpdateOrderShippingDetailsHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateOrderShippingDetailsCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());

        $trackingNumber = $command->getShippingTrackingNumber();
        $carrierId = $command->getNewCarrierId();
        $oldTrackingNumber = $order->shipping_number;

        $orderCarrier = new OrderCarrier($command->getCurrentOrderCarrierId());
        if (!Validate::isLoadedObject($orderCarrier)) {
            throw new OrderException('The order carrier ID is invalid.');
        }

        if (!empty($trackingNumber) && !Validate::isTrackingNumber($trackingNumber)) {
            throw new OrderException('The tracking number is incorrect.');
        }

        //update carrier - ONLY if changed - then refresh shipping cost
        $oldCarrierId = (int) $orderCarrier->id_carrier;
        if ($oldCarrierId !== $carrierId) {
            $order->id_carrier = (int) $carrierId;
            $orderCarrier->id_carrier = (int) $carrierId;
            $orderCarrier->update();
            $order->refreshShippingCost();
        }

        //load fresh order carrier because updated just before
        $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());

        // update shipping number
        // Keep these two following lines for backward compatibility, remove on 1.6 version
        $order->shipping_number = $trackingNumber;
        $order->update();

        // Update order_carrier
        $orderCarrier->tracking_number = pSQL($trackingNumber);
        if (!$orderCarrier->update()) {
            throw new OrderException('The order carrier cannot be updated.');
        }

        //send mail only if tracking number is different AND not empty
        if (!empty($trackingNumber) && $oldTrackingNumber != $trackingNumber) {
            if (!$orderCarrier->sendInTransitEmail($order)) {
                throw new OrderException('An error occurred while sending an email to the customer.');
            }

            $customer = new Customer((int) $order->id_customer);
            $carrier = new Carrier((int) $order->id_carrier, $order->id_lang);

            Hook::exec('actionAdminOrdersTrackingNumberUpdate', [
                'order' => $order,
                'customer' => $customer,
                'carrier' => $carrier,
            ], null, false, true, false, $order->id_shop);
        }
    }
}
