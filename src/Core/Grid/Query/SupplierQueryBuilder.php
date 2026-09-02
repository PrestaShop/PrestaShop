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
 * Class SupplierQueryBuilder builds search & count queries for suppliers grid.
 */
final class SupplierQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLangId
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextLangId,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->contextShopIds = $contextShopIds;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $filters = $searchCriteria->getFilters();
        $isCountFilter = array_key_exists('products_count', $filters);

        if ($isCountFilter) {
            $qb = $this->getQueryBuilderByProductsCount($filters);
            $qb->select('*');
            $this->applyFilters($qb, $filters, 'subQuery');
            $this->applyListQueryParameters($qb);
        } else {
            $qb = $this->getQueryBuilder();
            $this->applyListQuerySelection($qb);
            $this->applyListQueryParameters($qb);
            $this->applyFilters($qb, $filters, 's');
        }

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb)
        ;

        if ($isCountFilter) {
            $this->searchCriteriaApplicator->applyDeterministicSorting($searchCriteria, $qb, 'subQuery', 'id_supplier');
        } else {
            $this->searchCriteriaApplicator->applyDeterministicSorting($searchCriteria, $qb, 's', 'id_supplier');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $filters = $searchCriteria->getFilters();
        $isCountFilter = array_key_exists('products_count', $filters);

        if ($isCountFilter) {
            $alias = 'subQuery';
            $qb = $this->getQueryBuilderByProductsCount($filters);
            $this->applyFilters($qb, $filters, $alias);
            $this->applyListQueryParameters($qb);
        } else {
            $qb = $this->getQueryBuilder();
            $this->applyListQueryParameters($qb);
            $this->applyFilters($qb, $filters, 's');
            $alias = 's';
        }

        $qb->select('COUNT(DISTINCT ' . $alias . '.`id_supplier`)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'supplier', 's')
            ->innerJoin(
                's',
                $this->dbPrefix . 'supplier_lang',
                'sl',
                'sl.`id_supplier` = s.`id_supplier`'
            )
            ->innerJoin(
                's',
                $this->dbPrefix . 'supplier_shop',
                'ss',
                'ss.`id_supplier` = s.`id_supplier`'
            )
            ->leftJoin(
                's',
                $this->dbPrefix . 'product_supplier',
                'ps',
                'ps.`id_supplier` = s.`id_supplier`'
            )
            ->andWhere('sl.`id_lang` = :contextLangId')
            ->andWhere('ss.`id_shop` IN (:contextShopIds)')
        ;
    }

    /**
     * Gets query builder by product count which uses the main query as the sub-query in FROM condition.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilderByProductsCount(array $filters)
    {
        $subQuery = $this->getQueryBuilder();
        $this->applyListQuerySelection($subQuery);

        $alias = 'subQuery';

        $qb = $this->connection
            ->createQueryBuilder()
            ->from(
                '(' . $subQuery->getSQL() . ')',
                $alias
            )
            ->where('subQuery.`products_count` = :productsCountFilter')
        ;

        $qb->setParameter('productsCountFilter', $filters['products_count']);

        return $qb;
    }

    /**
     * Adds select and group by statements.
     *
     * @param QueryBuilder $qb
     */
    private function applyListQuerySelection(QueryBuilder $qb)
    {
        $qb
            ->select('s.`id_supplier`, s.`name`, s.`active`')
            ->addSelect('COUNT(DISTINCT ps.`id_product`) AS `products_count`')
            ->groupBy('s.`id_supplier`')
        ;
    }

    /**
     * Sets the parameters which are used in the queries.
     *
     * @param QueryBuilder $qb
     */
    private function applyListQueryParameters(QueryBuilder $qb)
    {
        $qb
            ->setParameter('contextLangId', $this->contextLangId)
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
        ;
    }

    /**
     * Adds filter restrictions.
     *
     * @param QueryBuilder $qb
     * @param array $filters
     * @param string $alias
     */
    private function applyFilters(QueryBuilder $qb, array $filters, $alias)
    {
        $availableFilters = [
            'id_supplier',
            'name',
            'active',
        ];

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $availableFilters, true)) {
                continue;
            }

            if (in_array($filterName, ['id_supplier', 'active'], true)) {
                $qb->andWhere($alias . '.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $value);

                continue;
            }

            $qb->andWhere($alias . '.`name` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $value . '%');
        }
    }
}
