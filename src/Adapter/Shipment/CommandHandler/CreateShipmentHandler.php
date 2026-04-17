<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler;

use Exception;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\CreateShipment;
use PrestaShop\PrestaShop\Core\Domain\Shipment\CommandHandler\CreateShipmentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;

#[AsCommandHandler]
class CreateShipmentHandler implements CreateShipmentHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly OrderRepository $orderRepository,
    ) {
    }

    public function handle(CreateShipment $command): int
    {
        try {
            $order = $this->orderRepository->get($command->getOrderId());
            $carrierId = $command->getCarrierId()->getValue();

            $shipment = new Shipment();
            $shipment->setOrderId((int) $order->id);
            $shipment->setCarrierId((int) $carrierId);
            $shipment->setAddressId((int) $order->id_address_delivery);
            $shipment->setTrackingNumber(null);
            // waiting for shippingCostCalculator, now we just set 0
            $shipment->setShippingCostTaxExcluded(0.00);
            $shipment->setShippingCostTaxIncluded(0.00);
            $shipment->setDeliveredAt(null);
            $shipment->setShippedAt(null);
            $shipment->setCancelledAt(null);

            return $this->shipmentRepository->save($shipment);
        } catch (Exception $e) {
            throw new ShipmentException('Failed to create shipment', $e->getCode(), $e);
        }
    }
}
