<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\Shipment;
use Throwable;

class ShipmentRepository extends EntityRepository
{
    /**
     * @var string
     */
    public $tablePrefix;

    public function setTablePrefix(string $tablePrefix): void
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @param int $orderId
     *
     * @return Shipment[]
     */
    public function findByOrderId(int $orderId)
    {
        return $this->findBy(['orderId' => $orderId, 'deleted' => false]);
    }

    /**
     * @param int $orderId
     *
     * @return Shipment[]
     */
    public function getAllShipmentsByOrderId(int $orderId)
    {
        return $this->findBy(['orderId' => $orderId]);
    }

    /**
     * @return array<int, array{
     *     id_shipment: int,
     *     quantity: int,
     * }>
     */
    public function findByOrderIdAndOrderDetailId(
        int $orderId,
        int $orderDetailId
    ): array {
        $conn = $this->getEntityManager()->getConnection();
        $qb = $conn->createQueryBuilder();
        $qb
            ->select(
                'sp.id_shipment',
                'sp.quantity AS quantity'
            )
            ->from($this->tablePrefix . 'shipment_product', 'sp')
            ->innerJoin(
                'sp',
                $this->tablePrefix . 'shipment',
                's',
                's.id_shipment = sp.id_shipment'
            )
            ->where('sp.id_order_detail = :orderDetailId')
            ->andWhere('s.id_order = :orderId')
            ->andWhere('s.deleted = false')
            ->groupBy('sp.id_shipment')
            ->setParameter('orderDetailId', $orderDetailId)
            ->setParameter('orderId', $orderId);

        return $qb->executeQuery()->fetchAllAssociative();
    }

    public function findByOrderAndShipmentId(int $orderId, int $shipmentId): ?Shipment
    {
        return $this->findOneBy(['orderId' => $orderId, 'id' => $shipmentId, 'deleted' => false]);
    }

    public function findById(int $shipmentId): ?Shipment
    {
        return $this->findOneBy(['id' => $shipmentId, 'deleted' => false]);
    }

    public function findByCarrierId(int $carrierId): array
    {
        return $this->findBy(['carrierId' => $carrierId, 'deleted' => false]);
    }

    public function save(Shipment $shipment): int
    {
        $this->getEntityManager()->persist($shipment);
        $this->getEntityManager()->flush();

        return $shipment->getId();
    }

    public function delete(Shipment $shipment): void
    {
        $shipment->setDeleted(true);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<int, array{
     *     id_shipment: int,
     *     id_order: int,
     *     id_carrier: int,
     *     id_delivery_address: int,
     *     shipping_cost_tax_excl: string,
     *     shipping_cost_tax_incl: string,
     *     packed_at: string|null,
     *     shipped_at: string|null,
     *     delivered_at: string|null,
     *     cancelled_at: string|null,
     *     tracking_number: string|null,
     *     date_add: string,
     *     date_upd: string,
     *     package_weight: string|null,
     *     carrier_name: string|null
     * }>
     */
    public function getShipmentWithWeightByOrderId(int $orderId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $qb = $conn->createQueryBuilder();
        $qb->select('s.*', 'SUM(od.product_weight * sp.quantity) as package_weight, c.name as carrier_name, c.url as carrier_tracking_url')
            ->from($this->tablePrefix . 'shipment', 's')
            ->leftJoin('s', $this->tablePrefix . 'shipment_product', 'sp', 's.id_shipment = sp.id_shipment')
            ->leftJoin('sp', $this->tablePrefix . 'order_detail', 'od', 'sp.id_order_detail = od.id_order_detail')
            ->leftJoin('s', $this->tablePrefix . 'carrier', 'c', 's.id_carrier = c.id_carrier')
            ->where('s.id_order = :orderId')
            ->andWhere('s.deleted = false')
            ->setParameter('orderId', $orderId)
            ->groupBy('s.id_shipment');

        return $qb->executeQuery()->fetchAllAssociative();
    }

    public function deleteShipmentProductByOrderAndOrderDetail(
        int $orderId,
        int $orderDetailId
    ): void {
        $conn = $this->getEntityManager()->getConnection();

        // Delete shipment products
        $conn->createQueryBuilder()
            ->delete($this->tablePrefix . 'shipment_product')
            ->where('id_order_detail = :orderDetailId')
            ->andWhere(
                'id_shipment IN (
                    SELECT id_shipment FROM ' . $this->tablePrefix . 'shipment WHERE id_order = :orderId
                )'
            )
            ->setParameter('orderDetailId', $orderDetailId)
            ->setParameter('orderId', $orderId)
            ->executeStatement();
    }

    public function deleteEmptyShipmentByOrder(
        int $orderId,
    ): void {
        $conn = $this->getEntityManager()->getConnection();

        // Soft delete empty shipments
        $conn->createQueryBuilder()
            ->update($this->tablePrefix . 'shipment')
            ->set('deleted', '1')
            ->where('id_order = :orderId')
            ->andWhere(
                'id_shipment NOT IN (
                    SELECT DISTINCT id_shipment FROM ' . $this->tablePrefix . 'shipment_product
                )'
            )
            ->setParameter('orderId', $orderId)
            ->executeStatement();
    }

    public function updateShipmentProductQuantity(
        int $shipmentId,
        int $orderDetailId,
        int $quantity
    ): void {
        $conn = $this->getEntityManager()->getConnection();

        $conn->beginTransaction();

        try {
            if ($quantity <= 0) {
                $conn->createQueryBuilder()
                    ->delete($this->tablePrefix . 'shipment_product')
                    ->where('id_shipment = :shipmentId')
                    ->andWhere('id_order_detail = :orderDetailId')
                    ->setParameter('shipmentId', $shipmentId)
                    ->setParameter('orderDetailId', $orderDetailId)
                    ->executeStatement();
            } else {
                $conn->createQueryBuilder()
                    ->update($this->tablePrefix . 'shipment_product')
                    ->set('quantity', ':quantity')
                    ->where('id_shipment = :shipmentId')
                    ->andWhere('id_order_detail = :orderDetailId')
                    ->setParameter('quantity', $quantity)
                    ->setParameter('shipmentId', $shipmentId)
                    ->setParameter('orderDetailId', $orderDetailId)
                    ->executeStatement();
            }

            $remainingProducts = $conn->createQueryBuilder()
                ->select('COUNT(*)')
                ->from($this->tablePrefix . 'shipment_product')
                ->where('id_shipment = :shipmentId')
                ->setParameter('shipmentId', $shipmentId)
                ->fetchOne();

            if ((int) $remainingProducts === 0) {
                $conn->createQueryBuilder()
                    ->update($this->tablePrefix . 'shipment')
                    ->set('deleted', '1')
                    ->where('id_shipment = :shipmentId')
                    ->setParameter('shipmentId', $shipmentId)
                    ->executeStatement();
            }

            $conn->commit();
        } catch (Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
