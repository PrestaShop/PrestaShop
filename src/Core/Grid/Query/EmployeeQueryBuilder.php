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
 * Class EmployeeQueryBuilder builds queries for Employees grid.
 */
final class EmployeeQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var string
     */
    private $contextIdLang;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param string $contextIdLang
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextIdLang,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextIdLang = $contextIdLang;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->getEmployeeQueryBuilder($searchCriteria)
            ->select('e.*, pl.name as profile_name');

        $this->searchCriteriaApplicator->applyPagination($searchCriteria, $searchQueryBuilder);
        $this->applySorting($searchCriteria, $searchQueryBuilder);

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $countQueryBuilder = $this->getEmployeeQueryBuilder($searchCriteria)
            ->select('COUNT(e.id_profile)');

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getEmployeeQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        // Check whether the employee is associated with one of the existing,
        // non-deleted shops from the current shop context.
        $contextShopAssociationSubquery = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'employee_shop', 'es_context')
            ->innerJoin(
                'es_context',
                $this->dbPrefix . 'shop',
                's_context',
                's_context.id_shop = es_context.id_shop AND s_context.deleted = 0'
            )
            ->where('e.id_employee = es_context.id_employee')
            ->andWhere('es_context.id_shop IN (:context_shop_ids)');

        // Only associations to existing, non-deleted shops are considered valid.
        // This is important because employee_shop may contain orphaned associations
        // pointing to missing or deleted shops.
        $validShopAssociationSubquery = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'employee_shop', 'es_valid')
            ->innerJoin(
                'es_valid',
                $this->dbPrefix . 'shop',
                's_valid',
                's_valid.id_shop = es_valid.id_shop AND s_valid.deleted = 0'
            )
            ->where('e.id_employee = es_valid.id_employee');

        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'employee', 'e')
            ->leftJoin(
                'e',
                $this->dbPrefix . 'profile_lang',
                'pl',
                'e.id_profile = pl.id_profile AND pl.id_lang = ' . (int) $this->contextIdLang
            )
            // Keep employees belonging to the current shop context, as well as
            // employees that are not associated with any valid shop. The latter
            // covers missing, orphaned and deleted shop associations without
            // weakening multistore scoping for employees assigned to another
            // existing shop.
            ->andWhere(
                sprintf(
                    '(EXISTS (%s) OR NOT EXISTS (%s))',
                    $contextShopAssociationSubquery->getSQL(),
                    $validShopAssociationSubquery->getSQL()
                )
            )
            ->setParameter(
                'context_shop_ids',
                $this->contextShopIds,
                Connection::PARAM_INT_ARRAY
            );

        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    /**
     * Apply filters for Query builder.
     *
     * @param QueryBuilder $queryBuilder
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $queryBuilder, array $filters)
    {
        $allowedFilters = [
            'id_employee',
            'firstname',
            'lastname',
            'email',
            'profile',
            'active',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('id_employee' === $filterName) {
                $queryBuilder->andWhere('e.id_employee = :' . $filterName);
                $queryBuilder->setParameter($filterName, $filterValue);

                continue;
            }

            if ('profile' === $filterName) {
                $queryBuilder->andWhere('pl.id_profile = :id_profile');
                $queryBuilder->setParameter('id_profile', $filterValue);

                continue;
            }

            if ('active' === $filterName) {
                $queryBuilder->andWhere('e.active = :active');
                $queryBuilder->setParameter('active', $filterValue);

                continue;
            }

            $queryBuilder->andWhere("`$filterName` LIKE :$filterName");
            $queryBuilder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param QueryBuilder $queryBuilder
     */
    private function applySorting(SearchCriteriaInterface $searchCriteria, QueryBuilder $queryBuilder)
    {
        if ($searchCriteria->getOrderBy() && $searchCriteria->getOrderWay()) {
            $orderBy = $searchCriteria->getOrderBy();

            if ('profile' === $orderBy) {
                $orderBy = 'pl.name';
            }

            $queryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
        }
    }
}
