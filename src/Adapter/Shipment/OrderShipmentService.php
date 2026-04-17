<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use Carrier;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;

class OrderShipmentService
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    public function __construct(ShipmentRepository $shipmentRepository, OrderRepository $orderRepository, CarrierRepository $carrierRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Returns the carrier used to ship a specific product within a given order.
     */
    public function getCarrierForProduct(int $orderId, int $productId): ?Carrier
    {
        $order = $this->orderRepository->get(new OrderId($orderId));
        $shipments = $this->shipmentRepository->findByOrderId($order->id);

        $orderDetails = $order->getOrderDetailList();
        $orderDetailId = null;

        foreach ($orderDetails as $orderDetail) {
            if ((int) $orderDetail['product_id'] === $productId) {
                $orderDetailId = (int) $orderDetail['id_order_detail'];
                break;
            }
        }

        foreach ($shipments as $shipment) {
            foreach ($shipment->getProducts() as $shipmentProduct) {
                if ($shipmentProduct->getOrderDetailId() === $orderDetailId) {
                    $carrierId = new CarrierId($shipment->getCarrierId());
                    $carrier = $this->carrierRepository->get($carrierId);

                    return $carrier;
                }
            }
        }

        return null;
    }

    /**
     * Returns all distinct carriers used to ship an order.
     *
     * @return Carrier[]
     */
    public function getAllCarriersForOrder(int $orderId): array
    {
        $shipments = $this->shipmentRepository->findByOrderId($orderId);

        $carriers = [];

        foreach ($shipments as $shipment) {
            if (!isset($carriers[$shipment->getCarrierId()])) {
                $carrierId = new CarrierId($shipment->getCarrierId());
                $carrier = $this->carrierRepository->get($carrierId);
                $carriers[$carrierId->getValue()] = $carrier;
            }
        }

        return $carriers;
    }

    public function orderHasShipment(int $orderId): bool
    {
        $shipments = $this->shipmentRepository->findByOrderId($orderId);

        return !empty($shipments);
    }
}
