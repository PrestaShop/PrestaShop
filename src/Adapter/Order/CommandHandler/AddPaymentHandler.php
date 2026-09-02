<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Currency;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\CommandHandler\AddPaymentHandlerInterface;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class AddPaymentHandler extends AbstractOrderHandler implements AddPaymentHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddPaymentCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());

        $currency = new Currency($command->getPaymentCurrencyId()->getValue());
        $orderHasInvoice = $order->hasInvoice();

        if ($orderHasInvoice) {
            $orderInvoice = new OrderInvoice($command->getOrderInvoiceId());
        } else {
            $orderInvoice = null;
        }

        if (!Validate::isLoadedObject($currency)) {
            throw new OrderException('The selected currency is invalid.');
        }

        if ($orderHasInvoice && !Validate::isLoadedObject($orderInvoice)) {
            throw new OrderException('The invoice is invalid.');
        }

        $paymentAdded = $order->addOrderPayment(
            (string) $command->getPaymentAmount(),
            $command->getPaymentMethod(),
            $command->getPaymentTransactionId(),
            $currency,
            $command->getPaymentDate()->format('Y-m-d H:i:s'),
            $orderInvoice,
            $command->getEmployeeId()->getValue()
        );

        if (!$paymentAdded) {
            throw new OrderException('An error occurred during payment.');
        }
    }
}
