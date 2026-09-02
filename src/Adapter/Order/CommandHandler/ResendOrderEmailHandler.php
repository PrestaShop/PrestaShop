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
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ResendOrderEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ResendOrderEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderEmailSendException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class ResendOrderEmailHandler extends AbstractOrderCommandHandler implements ResendOrderEmailHandlerInterface
{
    /**
     * @param ResendOrderEmailCommand $command
     */
    public function handle(ResendOrderEmailCommand $command): void
    {
        $order = $this->getOrder($command->getOrderId());
        $orderState = new OrderState($command->getOrderStatusId());

        if (!Validate::isLoadedObject($orderState)) {
            throw new OrderException(sprintf('An error occurred while loading order status. Order status with "%s" was not found.', $command->getOrderId()->getValue()));
        }

        $history = new OrderHistory($command->getOrderHistoryId());

        $carrier = new Carrier($order->id_carrier, (int) $order->getAssociatedLanguage()->getId());
        $templateVars = [];

        if ($orderState->id == Configuration::get('PS_OS_SHIPPING') && $order->getShippingNumber()) {
            $templateVars = ['{followup}' => str_replace('@', $order->getShippingNumber(), $carrier->url)];
        }

        if (!$history->sendEmail($order, $templateVars)) {
            throw new OrderEmailSendException('Failed to resend order email.', OrderEmailSendException::FAILED_RESEND);
        }
    }
}
