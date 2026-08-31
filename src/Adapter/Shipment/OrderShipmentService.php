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

    public function __construct(
        ShipmentRepository $shipmentRepository,
        OrderRepository $orderRepository,
        CarrierRepository $carrierRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Returns the carrier used to ship a specific product line within a given order.
     *
     * The same product and combination can appear on several order details, distinguished only by their
     * customization, and each of them can belong to a different shipment. All three identifiers are therefore
     * needed to resolve the order detail the carrier must be looked up for.
     */
    public function getCarrierForProduct(int $orderId, int $productId, int $combinationId = 0, int $customizationId = 0): ?Carrier
    {
        $order = $this->orderRepository->get(new OrderId($orderId));

        $orderDetail = $this->findOrderDetail($order->getOrderDetailList(), $productId, $combinationId, $customizationId);

        if ($orderDetail === null) {
            return null;
        }

        return $this->getCarrierForOrderDetail($orderId, (int) $orderDetail['id_order_detail']);
    }

    /**
     * Returns the carrier of the shipment the given order detail belongs to, or null when it belongs to none
     * (a virtual product for instance is never part of a shipment).
     */
    public function getCarrierForOrderDetail(int $orderId, int $orderDetailId): ?Carrier
    {
        $carrierId = $this->getCarrierIdsByOrderDetailId($orderId)[$orderDetailId] ?? null;

        return null !== $carrierId ? $this->carrierRepository->get(new CarrierId($carrierId)) : null;
    }

    /**
     * Returns the carrier of every given product line of an order, keyed by the very same keys as the given
     * lines so callers can look them up while iterating.
     *
     * Callers displaying a carrier per product line must use this rather than calling getCarrierForProduct()
     * in a loop: the order, its details and its shipments are then read once instead of once per line.
     *
     * @param array<array-key, array<string, mixed>> $productLines cart product lines, as held by Order::$product_list
     *
     * @return array<array-key, Carrier> lines shipped by no shipment are absent from the result
     */
    public function getCarriersForProductLines(int $orderId, array $productLines): array
    {
        $carrierIdsByOrderDetailId = $this->getCarrierIdsByOrderDetailId($orderId);
        if (empty($carrierIdsByOrderDetailId)) {
            return [];
        }

        $orderDetails = $this->orderRepository->get(new OrderId($orderId))->getOrderDetailList();

        $carriers = [];
        $loadedCarriers = [];
        foreach ($productLines as $key => $productLine) {
            $orderDetail = $this->findOrderDetail(
                $orderDetails,
                (int) $productLine['id_product'],
                (int) ($productLine['id_product_attribute'] ?? 0),
                (int) ($productLine['id_customization'] ?? 0)
            );
            if ($orderDetail === null) {
                continue;
            }

            $carrierId = $carrierIdsByOrderDetailId[(int) $orderDetail['id_order_detail']] ?? null;
            if ($carrierId === null) {
                continue;
            }

            if (!isset($loadedCarriers[$carrierId])) {
                $loadedCarriers[$carrierId] = $this->carrierRepository->get(new CarrierId($carrierId));
            }
            $carriers[$key] = $loadedCarriers[$carrierId];
        }

        return $carriers;
    }

    /**
     * An order can hold several order details for the same product and combination, they are then only
     * distinguished by their customization, so all three identifiers are part of the criteria.
     *
     * @param array<array<string, mixed>> $orderDetails
     *
     * @return array<string, mixed>|null
     */
    private function findOrderDetail(array $orderDetails, int $productId, int $combinationId, int $customizationId): ?array
    {
        foreach ($orderDetails as $orderDetail) {
            if (
                (int) $orderDetail['product_id'] === $productId
                && (int) ($orderDetail['product_attribute_id'] ?? 0) === $combinationId
                && (int) ($orderDetail['id_customization'] ?? 0) === $customizationId
            ) {
                return $orderDetail;
            }
        }

        return null;
    }

    /**
     * @return array<int, int> carrier id, keyed by order detail id
     */
    private function getCarrierIdsByOrderDetailId(int $orderId): array
    {
        $carrierIds = [];
        foreach ($this->shipmentRepository->findByOrderId($orderId) as $shipment) {
            foreach ($shipment->getProducts() as $shipmentProduct) {
                $carrierIds[$shipmentProduct->getOrderDetailId()] = $shipment->getCarrierId();
            }
        }

        return $carrierIds;
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
