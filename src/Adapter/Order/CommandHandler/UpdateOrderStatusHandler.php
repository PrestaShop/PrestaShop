<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Carrier;
use Configuration;
use OrderHistory;
use OrderState;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\UpdateOrderStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ChangeOrderStatusException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Mutation\MutationTracker;
use PrestaShopBundle\Entity\MutationAction;

/**
 * @internal
 */
#[AsCommandHandler]
final class UpdateOrderStatusHandler extends AbstractOrderHandler implements UpdateOrderStatusHandlerInterface
{
    public function __construct(
        private EmployeeContext $employeeContext,
        private MutationTracker $mutationTracker,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateOrderStatusCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());
        $orderState = $this->getOrderStateObject($command->getNewOrderStatusId());

        /*
         * Try to load current order status. There may be cases when there is none, for example when something happens
         * during order creation process. That's why we check for $currentOrderState validity.
         */
        $currentOrderState = $order->getCurrentOrderState();
        if (!empty($currentOrderState) && $currentOrderState->id == $orderState->id) {
            throw new OrderException('The order has already been assigned this status.');
        }

        // Create new OrderHistory
        $history = new OrderHistory();
        $history->id_order = $order->id;
        $history->id_employee = (int) $this->employeeContext->getEmployee()?->getId();

        $useExistingPayments = false;
        if (!$order->hasInvoice()) {
            $useExistingPayments = true;
        }

        $history->changeIdOrderState((int) $orderState->id, $order, $useExistingPayments);

        $templateVars = [];

        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->getShippingNumber()) {
            $carrier = new Carrier($order->id_carrier, (int) $order->getAssociatedLanguage()->getId());
            $templateVars = [
                '{followup}' => str_replace('@', $order->getShippingNumber(), $carrier->url),
            ];
        }

        // Save all changes
        if ($history->addWithemail(true, $templateVars)) {
            $this->mutationTracker->addMutationForApiClient('order_history', (int) $history->id, MutationAction::CREATE);

            return;
        }

        throw new ChangeOrderStatusException([], [$command->getOrderId()], [], 'Failed to update status or sent email when changing order status.');
    }

    /**
     * @param int $orderStatusId
     *
     * @return OrderState
     */
    private function getOrderStateObject($orderStatusId)
    {
        $orderState = new OrderState($orderStatusId);

        if ($orderState->id !== $orderStatusId) {
            throw new OrderException(sprintf('Order status with id "%s" was not found.', $orderStatusId));
        }

        return $orderState;
    }
}
