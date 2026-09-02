<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Address;
use Cart;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Adapter\Order\OrderDetailUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderInvoiceAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ChangeOrderInvoiceAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class ChangeOrderInvoiceAddressHandler extends AbstractOrderHandler implements ChangeOrderInvoiceAddressHandlerInterface
{
    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;

    /**
     * @var OrderDetailUpdater
     */
    private $orderDetailTaxUpdater;

    /**
     * @param OrderAmountUpdater $orderAmountUpdater
     * @param OrderDetailUpdater $orderDetailTaxUpdater
     */
    public function __construct(
        OrderAmountUpdater $orderAmountUpdater,
        OrderDetailUpdater $orderDetailTaxUpdater
    ) {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->orderDetailTaxUpdater = $orderDetailTaxUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ChangeOrderInvoiceAddressCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());
        $address = new Address($command->getNewInvoiceAddressId()->getValue());

        $cart = Cart::getCartByOrderId($order->id);

        if (!Validate::isLoadedObject($address)) {
            throw new OrderException('New invoice address is not valid');
        }

        $cart->id_address_invoice = $address->id;
        $cart->update();

        $order->id_address_invoice = $address->id;
        $this->orderDetailTaxUpdater->updateOrderDetailsTaxes($order);
        $this->orderAmountUpdater->update($order, $cart);
    }
}
