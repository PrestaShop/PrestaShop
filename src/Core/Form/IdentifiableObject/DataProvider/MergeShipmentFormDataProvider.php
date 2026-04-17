<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetOrderShipments;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Query\GetShipmentProducts;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\OrderShipment;
use PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult\OrderShipmentProduct;
use Symfony\Component\HttpFoundation\RequestStack;

class MergeShipmentFormDataProvider extends ShipmentFormDataProvider
{
    public function __construct(
        private CommandBusInterface $queryBus,
        private RequestStack $requestStack
    ) {
        parent::__construct($queryBus);
    }

    public function getData($orderId)
    {
        $shipmentId = $this->requestStack->getMainRequest()->query->getInt('shipmentId');

        /** @var OrderShipmentProduct[] $products */
        $products = $this->queryBus->handle(new GetShipmentProducts($shipmentId));

        /** @var OrderShipment[] $shipments */
        $shipments = $this->queryBus->handle(new GetOrderShipments($orderId));

        $shipments = array_filter($shipments, fn (OrderShipment $s) => $s->getId() !== $shipmentId);

        foreach ($products as &$p) {
            $p = $p->toArray();
        }

        return [
            'products' => $products,
            'shipments' => $shipments,
            'is_shipped' => $this->isShipmentShipped($orderId, $shipmentId),
        ];
    }

    public function getDefaultData()
    {
        return [];
    }
}
