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

use Carrier;
use Configuration;
use Context;
use Order;
use OrderHistory;
use OrderState;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\BulkChangeOrderStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ChangeOrderStatusException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use StockAvailable;

/**
 * @internal
 */
final class BulkChangeOrderStatusHandler implements BulkChangeOrderStatusHandlerInterface
{
    /**
     * @param BulkChangeOrderStatusCommand $command
     */
    public function handle(BulkChangeOrderStatusCommand $command)
    {
        $orderState = new OrderState($command->getNewOrderStatusId());

        if ($orderState->id !== $command->getNewOrderStatusId()) {
            throw new OrderException(sprintf('Order state with ID "%s" was not found.', $command->getNewOrderStatusId()));
        }

        $ordersWithFailedToUpdateStatus = [];
        $ordersWithFailedToSendEmail = [];
        $ordersWithAssignedStatus = [];

        foreach ($command->getOrderIds() as $orderId) {
            $order = $this->getOrderObject($orderId);
            $currentOrderState = $order->getCurrentOrderState();

            if ($currentOrderState->id === $orderState->id) {
                $ordersWithAssignedStatus[] = $orderId;

                continue;
            }

            $history = new OrderHistory();
            $history->id_order = $order->id;
            $history->id_employee = (int) Context::getContext()->employee->id;

            $useExistingPayment = !$order->hasInvoice();
            $history->changeIdOrderState((int) $orderState->id, $order, $useExistingPayment);

            $carrier = new Carrier($order->id_carrier, (int) $order->getAssociatedLanguage()->getId());
            $templateVars = [];

            if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->getShippingNumber()) {
                $templateVars['{followup}'] = str_replace('@', $order->getShippingNumber(), $carrier->url);
            }

            if (!$history->add()) {
                $ordersWithFailedToUpdateStatus[] = $orderId;

                continue;
            }

            if (!$history->sendEmail($order, $templateVars)) {
                $ordersWithFailedToSendEmail[] = $orderId;

                continue;
            }

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                foreach ($order->getProducts() as $product) {
                    if (StockAvailable::dependsOnStock($product['product_id'])) {
                        StockAvailable::synchronize($product['product_id'], (int) $product['id_shop']);
                    }
                }
            }
        }

        if (!empty($ordersWithFailedToUpdateStatus)
            || !empty($ordersWithFailedToSendEmail)
            || !empty($ordersWithAssignedStatus)
        ) {
            throw new ChangeOrderStatusException($ordersWithFailedToUpdateStatus, $ordersWithFailedToSendEmail, $ordersWithAssignedStatus, 'Failed to update status or sent email when changing order status.');
        }
    }

    /**
     * @param OrderId $orderId
     *
     * @return Order
     */
    private function getOrderObject(OrderId $orderId)
    {
        $order = new Order($orderId->getValue());

        if ($order->id !== $orderId->getValue()) {
            throw new OrderNotFoundException($orderId);
        }

        return $order;
    }
}
