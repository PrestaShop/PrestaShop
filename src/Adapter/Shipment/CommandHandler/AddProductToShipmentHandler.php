<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use Exception;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderDetailRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderDetailNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\AddProductToShipment;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\AddProductToShipmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\ShipmentProduct;

#[AsCommandHandler]
class AddProductToShipmentHandler implements AddProductToShipmentHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly OrderDetailRepository $orderDetailRepository
    ) {
    }

    public function handle(AddProductToShipment $command): void
    {
        $shipmentId = $command->getShipmentId()->getValue();
        $shipment = $this->shipmentRepository->findById($shipmentId);

        if ($shipment === null) {
            throw new ShipmentNotFoundException(sprintf('No shipment with id %s found', $shipment));
        }

        $orderDetail = $this->orderDetailRepository->findByOrderIdAndProductId($command->getOrderId(), $command->getProductId(), $command->getCombinationId());

        if ($orderDetail === null) {
            throw new OrderDetailNotFoundException(null, sprintf('No order detail for order id %s and product id %s found', $command->getOrderId()->getValue(), $command->getProductId()->getValue()));
        }

        $shipmentProduct = new ShipmentProduct();
        $shipmentProduct->setOrderDetailId($orderDetail->id_order_detail);
        $shipmentProduct->setQuantity($orderDetail->product_quantity);
        $shipment->addShipmentProduct($shipmentProduct);

        try {
            $this->shipmentRepository->save($shipment);
        } catch (Exception $e) {
            throw new ShipmentException(sprintf('Failed to add products from shipment with id "%s"', $shipmentId), 0, $e);
        }
    }
}
