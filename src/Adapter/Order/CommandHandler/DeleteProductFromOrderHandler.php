<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Cart;
use Currency;
use Customer;
use Exception;
use Hook;
use Order;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\OrderProductQuantityUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\DeleteProductFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\DeleteProductFromOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Shop;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class DeleteProductFromOrderHandler extends AbstractOrderCommandHandler implements DeleteProductFromOrderHandlerInterface
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;
    /**
     * @var OrderProductQuantityUpdater
     */
    private $orderProductQuantityUpdater;

    /**
     * @param ContextStateManager $contextStateManager
     * @param OrderProductQuantityUpdater $orderProductQuantityUpdater
     */
    public function __construct(
        ContextStateManager $contextStateManager,
        OrderProductQuantityUpdater $orderProductQuantityUpdater,
        private readonly ?ShipmentRepository $shipmentRepository = null,
    ) {
        $this->contextStateManager = $contextStateManager;
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteProductFromOrderCommand $command)
    {
        $orderDetail = new OrderDetail($command->getOrderDetailId());
        $order = new Order($command->getOrderId()->getValue());

        $this->assertProductCanBeDeleted($order, $orderDetail);

        $cart = new Cart($order->id_cart);

        $this->contextStateManager
            ->setCart($cart)
            ->setCurrency(new Currency($order->id_currency))
            ->setCustomer(new Customer($order->id_customer))
            ->setShop(new Shop($order->id_shop))
        ;

        try {
            $order = $this->orderProductQuantityUpdater->update(
                $order,
                $orderDetail,
                0,
                $orderDetail->id_order_invoice != 0 ? new OrderInvoice($orderDetail->id_order_invoice) : null
            );

            Hook::exec('actionOrderEdited', ['order' => $order]);
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }

        if ($this->shipmentRepository) {
            $this->handleShipmentDeletion($command);
        } else {
            trigger_deprecation('prestashop/prestashop', '9.2', 'ShipmentRepository must be set.');
        }
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     */
    private function assertProductCanBeDeleted(Order $order, OrderDetail $orderDetail)
    {
        if (!Validate::isLoadedObject($orderDetail)) {
            throw new OrderException('Order detail could not be found.');
        }

        if (!Validate::isLoadedObject($order)) {
            throw new OrderNotFoundException(new OrderId((int) $order->id), 'Order could not be found.');
        }

        if ($orderDetail->id_order != $order->id) {
            throw new OrderException('Order detail does not belong to order.');
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            throw new OrderException('Delivered order cannot be modified.');
        }
    }

    private function handleShipmentDeletion(DeleteProductFromOrderCommand $command): void
    {
        $orderId = $command->getOrderId()->getValue();
        $orderDetailId = $command->getOrderDetailId();

        try {
            $this->shipmentRepository->deleteShipmentProductByOrderAndOrderDetail($orderId, $orderDetailId);
            $this->shipmentRepository->deleteEmptyShipmentByOrder($orderId);
        } catch (Exception $e) {
            throw new ShipmentException(sprintf('Failed to delete shipment product from order with id "%s"', $orderId), 0, $e);
        }
    }
}
