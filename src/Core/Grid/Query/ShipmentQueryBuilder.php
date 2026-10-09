<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentStatus;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ShipmentFilters;

/**
 * Provides SQL for the shop-wide shipments listing.
 *
 * Not to be confused with OrderShipmentQueryBuilder, which feeds the shipments grid embedded in a
 * single order view.
 *
 * @internal The grid id 'shipment' was the per-order shipments grid up to 9.2, and it now names this
 *           shop-wide one. The per-order grid moved to 'order_shipment'. That reassignment also moves
 *           the hooks derived from the id, actionShipmentGrid*Modifier, onto this grid. It is a
 *           deliberate break, accepted because the improved_shipment feature flag is beta and off by
 *           default; these classes carry no backward compatibility promise while it stays that way.
 */
final class ShipmentQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const STRICT_COMPARISON_FILTERS = [
        'id_shipment' => 's.`id_shipment`',
        'carrier' => 'c.`name`',
    ];

    /**
     * Aliases the grid may be sorted on. Anything else falls back to the default ordering: this grid
     * took over the 'shipment' filter id from the per-order one, so shops upgrading may still have a
     * stale ps_admin_filter row pointing at a column that no longer exists.
     */
    private const SORTABLE_FIELDS = [
        'carrier',
        'customer',
        'date_add',
        'id_shipment',
        'items',
        'order_reference',
        'shipping_cost',
        'status',
        'tracking_number',
    ];

    private const LIKE_COMPARISON_FILTERS = [
        'order_reference' => 'o.`reference`',
        'tracking_number' => 's.`tracking_number`',
    ];

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly ShopContext $shopContext,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria->getFilters())
            ->select([
                's.`id_shipment`',
                's.`id_order`',
                's.`date_add`',
                's.`tracking_number`',
                's.`packed_at`',
                's.`shipping_cost_tax_incl` AS `shipping_cost`',
                'o.`reference` AS `order_reference`',
                'c.`name` AS `carrier`',
                'c.`url` AS `carrier_url`',
                'sh.`name` AS `shop_name`',
                'os.`paid` AS `is_paid`',
                self::getCustomerField() . ' AS `customer`',
                ShipmentStatus::getSqlExpression() . ' AS `status`',
                'SUM(sp.`quantity`) AS `items`',
                'SUM(od.`product_weight` * sp.`quantity`) AS `weight`',
            ])
            ->groupBy('s.`id_shipment`')
        ;

        $this->searchCriteriaApplicator->applyPagination($searchCriteria, $qb);
        $this->applySorting($qb, $searchCriteria);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT s.`id_shipment`)');
    }

    private function applySorting(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        $orderBy = $searchCriteria->getOrderBy();
        $orderWay = $searchCriteria->getOrderWay();

        if (!in_array($orderBy, self::SORTABLE_FIELDS, true)) {
            $orderBy = ShipmentFilters::getDefaults()['orderBy'];
            $orderWay = ShipmentFilters::getDefaults()['sortOrder'];
        }

        $qb->orderBy(sprintf('`%s`', $orderBy), $orderWay);

        if ($orderBy !== 'id_shipment') {
            // Shipments created within the same second must not shuffle from one page to the next.
            $qb->addOrderBy('s.`id_shipment`', $orderWay);
        }
    }

    private function getBaseQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'shipment', 's')
            ->innerJoin('s', $this->dbPrefix . 'orders', 'o', 's.`id_order` = o.`id_order`')
            ->leftJoin('s', $this->dbPrefix . 'carrier', 'c', 's.`id_carrier` = c.`id_carrier`')
            ->leftJoin('s', $this->dbPrefix . 'shipment_product', 'sp', 's.`id_shipment` = sp.`id_shipment`')
            ->leftJoin('sp', $this->dbPrefix . 'order_detail', 'od', 'sp.`id_order_detail` = od.`id_order_detail`')
            ->leftJoin('o', $this->dbPrefix . 'customer', 'cu', 'o.`id_customer` = cu.`id_customer`')
            ->leftJoin('o', $this->dbPrefix . 'shop', 'sh', 'o.`id_shop` = sh.`id_shop`')
            ->leftJoin('o', $this->dbPrefix . 'order_state', 'os', 'o.`current_state` = os.`id_order_state`')
            ->andWhere('s.`deleted` = 0')
            // ps_shipment carries no id_shop of its own: multistore scoping goes through its order.
            ->andWhere('o.`id_shop` IN (:context_shop_ids)')
            ->setParameter('context_shop_ids', $this->shopContext->getAssociatedShopIds(), Connection::PARAM_INT_ARRAY)
        ;

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        foreach ($filters as $filterName => $filterValue) {
            if (isset(self::STRICT_COMPARISON_FILTERS[$filterName])) {
                $qb->andWhere(sprintf('%s = :%s', self::STRICT_COMPARISON_FILTERS[$filterName], $filterName));
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if (isset(self::LIKE_COMPARISON_FILTERS[$filterName])) {
                $qb->andWhere(sprintf('%s LIKE :%s', self::LIKE_COMPARISON_FILTERS[$filterName], $filterName));
                $qb->setParameter($filterName, '%' . $this->escapePercent((string) $filterValue) . '%');

                continue;
            }

            if ($filterName === 'customer') {
                $qb->andWhere(self::getCustomerField() . ' LIKE :customer');
                $qb->setParameter('customer', '%' . $this->escapePercent((string) $filterValue) . '%');

                continue;
            }

            if ($filterName === 'status') {
                // Filtering on the very expression the status is derived from keeps display, sorting
                // and filtering in sync.
                $qb->andWhere(ShipmentStatus::getSqlExpression() . ' = :status');
                $qb->setParameter('status', $filterValue);

                continue;
            }

            if ($filterName === 'date_add') {
                if (!empty($filterValue['from'])) {
                    $qb->andWhere('s.`date_add` >= :date_add_from');
                    $qb->setParameter('date_add_from', sprintf('%s 0:0:0', $filterValue['from']));
                }

                if (!empty($filterValue['to'])) {
                    $qb->andWhere('s.`date_add` <= :date_add_to');
                    $qb->setParameter('date_add_to', sprintf('%s 23:59:59', $filterValue['to']));
                }
            }
        }
    }

    private static function getCustomerField(): string
    {
        return 'CONCAT(cu.`firstname`, \' \', cu.`lastname`)';
    }
}
