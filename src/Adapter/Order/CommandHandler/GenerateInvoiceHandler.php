<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Configuration;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\CommandHandler\GenerateOrderInvoiceHandlerInterface;

/**
 * @internal
 */
#[AsCommandHandler]
final class GenerateInvoiceHandler extends AbstractOrderHandler implements GenerateOrderInvoiceHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GenerateInvoiceCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());

        if (!Configuration::get('PS_INVOICE', null, null, $order->id_shop)) {
            throw new OrderException('Invoice management has been disabled.');
        }

        if ($order->hasInvoice()) {
            throw new OrderException('This order already has an invoice.');
        }

        $order->setInvoice(true);
    }
}
