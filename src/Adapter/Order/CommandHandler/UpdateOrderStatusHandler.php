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

        $currentOrderState = $order->getCurrentOrderState();

        if ($currentOrderState->id == $orderState->id) {
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

        $carrier = new Carrier($order->id_carrier, (int) $order->getAssociatedLanguage()->getId());
        $templateVars = [];

        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->getShippingNumber()) {
            $templateVars = [
                '{followup}' => str_replace('@', $order->getShippingNumber(), $carrier->url),
            ];
        }

        // Save all changes
        if ($history->addWithemail(true, $templateVars)) {
            $this->mutationTracker->addMutation('order_history', (int) $history->id, MutationTracker::CREATE_ACTION);

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
