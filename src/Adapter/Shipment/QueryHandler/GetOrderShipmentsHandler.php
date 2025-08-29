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
use Throwable;

#[AsQueryHandler]
class GetOrderShipmentsHandler implements GetOrderShipmentsHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly CarrierRepository $carrierRepository,
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
            $result = $this->shipmentRepository->findByOrderId($orderId);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(sprintf('Could not find shipment for order with id "%s"', $orderId), 0, $e);
        }

        foreach ($result as $shipment) {
            try {
                $carrier = $this->carrierRepository->get(new CarrierId($shipment->getCarrierId()));
            } catch (Throwable $e) {
                throw new ShipmentNotFoundException(sprintf('Could not find carrier with id "%s"', $shipment->getCarrierId()), 0, $e);
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
