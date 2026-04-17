<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Provides SQL for shipments listing.
 */
final class ShipmentQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ALLOWED_FILTERS = [
        'shipment_number',
        'carrier',
        'tracking_number',
        'date',
        'order_id',
    ];

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria)
            ->select([
                's.id_shipment AS shipment_number',
                's.date_add AS date',
                's.id_order AS order_id',
                'c.name AS carrier',
                's.tracking_number',
                'SUM(sp.quantity) AS items',
                's.shipping_cost_tax_incl AS shipping_cost',
                'SUM(od.product_weight * sp.quantity) AS weight',
            ])
            ->groupBy('s.id_shipment');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQueryBuilder($searchCriteria)
            ->select('COUNT(DISTINCT s.id_shipment)');
    }

    private function getBaseQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'shipment', 's')
            ->leftJoin('s', $this->dbPrefix . 'carrier', 'c', 's.id_carrier = c.id_carrier')
            ->leftJoin('s', $this->dbPrefix . 'shipment_product', 'sp', 's.id_shipment = sp.id_shipment')
            ->leftJoin('sp', $this->dbPrefix . 'order_detail', 'od', 'sp.id_order_detail = od.id_order_detail')
            ->andWhere('s.deleted = false');

        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, self::ALLOWED_FILTERS)) {
                continue;
            }

            if ($filterName === 'carrier') {
                $qb->andWhere('c.name LIKE :carrier');
                $qb->setParameter('carrier', '%' . $filterValue . '%');
            } elseif ($filterName === 'date') {
                $qb->andWhere('DATE(s.date_add) = :date');
                $qb->setParameter('date', $filterValue);
            } elseif ($filterName === 'shipment_number') {
                $qb->andWhere('s.id_shipment = :shipment_number');
                $qb->setParameter('shipment_number', $filterValue);
            } elseif ($filterName === 'tracking_number') {
                $qb->andWhere('s.tracking_number LIKE :tracking_number');
                $qb->setParameter('tracking_number', '%' . $filterValue . '%');
            } elseif ($filterName === 'order_id') {
                $qb->andWhere('s.id_order = :order_id');
                $qb->setParameter('order_id', (int) $filterValue);
            }
        }
    }
}
