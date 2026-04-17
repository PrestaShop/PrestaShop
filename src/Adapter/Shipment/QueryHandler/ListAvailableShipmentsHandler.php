<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use Carrier;
use OrderDetail;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\ListAvailableShipments;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\ListAvailableShipmentsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentsForMerge;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Product;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsQueryHandler]
class ListAvailableShipmentsHandler implements ListAvailableShipmentsHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $repository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param ListAvailableShipments $query
     *
     * @return ShipmentsForMerge[]
     */
    public function handle(ListAvailableShipments $query)
    {
        $shipments = [];
        $orderId = $query->getOrderId()->getValue();
        $orderDetailsIds = $query->getOrderIdDetails()->getValue();

        try {
            $getShipmentsFromOrder = $this->repository->findByOrderId($orderId);
        } catch (Throwable $e) {
            throw new ShipmentNotFoundException(
                $this->translator->trans(
                    'Could not find shipment for order id "%id%".',
                    ['%id%' => $orderId],
                    'Admin.Shipment.Error'
                ),
                0,
                $e
            );
        }

        if (empty($getShipmentsFromOrder)) {
            return $shipments;
        }

        foreach ($getShipmentsFromOrder as $shipment) {
            foreach ($orderDetailsIds as $orderDetailId) {
                $orderDetail = new OrderDetail($orderDetailId);
                $carrierCompatibleWithProduct = array_map(function ($carrier) {
                    return $carrier['id_carrier'];
                }, (new Product($orderDetail->product_id))->getCarriers());

                if ($shipment->getDeliveredAt() === null) {
                    $isCompatible = in_array($shipment->getCarrierId(), $carrierCompatibleWithProduct);
                    $shipmentName = $this->translator->trans('Shipment ', [], 'Shop.Forms.Labels')
                        . $shipment->getId()
                        . ' '
                        . (new Carrier($shipment->getCarrierId()))->name;

                    $shipments[] = new ShipmentsForMerge(
                        $shipment->getId(),
                        $shipmentName,
                        $isCompatible
                    );
                }
            }
        }

        return $shipments;
    }
}
