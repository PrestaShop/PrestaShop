<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use OrderDetail;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\GetShipmentForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentForEditing;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[AsQueryHandler]
class GetShipmentForEditingHandler implements GetShipmentForEditingHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param GetShipmentForEditing $query
     *
     * @return ShipmentForEditing
     */
    public function handle(GetShipmentForEditing $query)
    {
        $shipmentDetails = [];
        $orderId = $query->getOrderId()->getValue();
        $shipmentId = $query->getShipmentId()->getValue();

        try {
            $result = $this->shipmentRepository->findByOrderAndShipmentId($orderId, $shipmentId);
            $shipmentProducts = $result->getProducts()->toArray();
            $shipmentDetails['tracking_number'] = $result->getTrackingNumber();
            $shipmentDetails['carrier'] = $result->getCarrierId();

            foreach ($shipmentProducts as $shipmentProduct) {
                $shipmentDetails['selectedProducts'][
                    (new OrderDetail($shipmentProduct->getOrderDetailId()))->product_id
                ] = 0;
            }
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

        return new ShipmentForEditing(
            $shipmentDetails['carrier'],
            $shipmentDetails['tracking_number'] ?? '',
            $shipmentDetails['selectedProducts']
        );
    }
}
