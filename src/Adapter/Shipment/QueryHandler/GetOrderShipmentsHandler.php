<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierSummary;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetOrderShipments;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\GetOrderShipmentsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\OrderShipment;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsQueryHandler]
class GetOrderShipmentsHandler implements GetOrderShipmentsHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly CarrierRepository $carrierRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param GetOrderShipments $query
     *
     * @return OrderShipment[]
     */
    public function handle(GetOrderShipments $query)
    {
        $shipments = [];
        $orderId = $query->getOrderId()->getValue();

        try {
            $result = $query->includeDeleted()
                ? $this->shipmentRepository->getAllShipmentsByOrderId($orderId)
                : $this->shipmentRepository->findByOrderId($orderId);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment for order with id "%id%".',
                    ['%id%' => $orderId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }

        foreach ($result as $shipment) {
            try {
                $carrier = $this->carrierRepository->get(new CarrierId($shipment->getCarrierId()));
            } catch (Throwable $e) {
                throw new ShipmentNotFoundException(
                    $this->translator->trans(
                        'Could not find carrier with id "%id%".',
                        ['%id%' => $shipment->getCarrierId()],
                        'Admin.Shipment.Error'
                    ),
                    0,
                    $e
                );
            }

            $carrierSummary = new CarrierSummary($carrier->id, $carrier->name);

            $shipments[] = new OrderShipment(
                $shipment->getId(),
                $shipment->getOrderId(),
                $carrierSummary,
                $shipment->getAddressId(),
                new DecimalNumber((string) $shipment->getShippingCostTaxExcluded()),
                new DecimalNumber((string) $shipment->getShippingCostTaxIncluded()),
                $shipment->getProducts()->count(),
                $shipment->getTrackingNumber(),
                $shipment->getShippedAt(),
                $shipment->getDeliveredAt(),
                $shipment->getCancelledAt(),
            );
        }

        return $shipments;
    }
}
