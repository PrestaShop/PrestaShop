<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shipment\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentsForOrderDetail;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryHandler\GetShipmentsForOrderDetailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\ShipmentForOrderDetail;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;

#[AsQueryHandler]
class GetShipmentsForOrderDetailHandler implements GetShipmentsForOrderDetailHandlerInterface
{
    public function __construct(
        private readonly ShipmentRepository $repository,
    ) {
    }

    /**
     * @return ShipmentForOrderDetail[]
     */
    public function handle(GetShipmentsForOrderDetail $query)
    {
        $results = $this->repository->findByOrderIdAndOrderDetailId($query->getOrderId()->getValue(), $query->getOrderDetailId()->getValue());

        $shipmentsForOrderDetail = [];

        foreach ($results as $key => $value) {
            $shipmentsForOrderDetail[] = new ShipmentForOrderDetail($value['id_shipment'], $value['quantity']);
        }

        return $shipmentsForOrderDetail;
    }
}
