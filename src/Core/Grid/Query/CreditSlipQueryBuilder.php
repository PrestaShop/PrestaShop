<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class responsible for providing sql for credit slip list
 */
final class CreditSlipQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());

        $qb
            ->select('slip.id_order_slip, slip.id_order, slip.date_add')
            ->groupBy('slip.id_order_slip')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT slip.`id_order_slip`)')
        ;

        return $qb;
    }

    /**
     * Gets query builder with the common sql for credit slip listing.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'order_slip', 'slip')
            ->leftJoin(
                'slip',
                $this->dbPrefix . 'orders',
                'orders',
                'slip.id_order = orders.id_order'
            )
        ;
        $qb->andWhere('orders.id_shop IN (:contextShopIds)');
        $qb->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $availableFiltersMap = [
            'id_credit_slip' => 'slip.id_order_slip',
            'id_order' => 'slip.id_order',
            'date_issued' => 'slip.date_add',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $availableFiltersMap)) {
                continue;
            }

            if ('id_credit_slip' === $filterName || 'id_order' === $filterName) {
                $qb->andWhere($availableFiltersMap[$filterName] . "= :$filterName");
                $qb->setParameter($filterName, $value);

                continue;
            }

            if ('date_issued' === $filterName) {
                if (isset($value['from'])) {
                    $qb->andWhere($availableFiltersMap[$filterName] . ' >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $value['from']));
                }
                if (isset($value['to'])) {
                    $qb->andWhere($availableFiltersMap[$filterName] . ' <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $value['to']));
                }
                continue;
            }
        }
    }
}
