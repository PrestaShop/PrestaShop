<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Carrier;
use Configuration;
use Context;
use Order;
use OrderHistory;
use OrderState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\BulkChangeOrderStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ChangeOrderStatusException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * @internal
 */
#[AsCommandHandler]
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
