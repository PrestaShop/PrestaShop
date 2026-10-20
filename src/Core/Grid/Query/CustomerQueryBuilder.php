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
 * Class CustomerQueryBuilder builds queries to fetch data for customers grid.
 */
final class CustomerQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
     * @param int $contextLangId
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        $contextLangId,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->contextShopIds = $contextShopIds;
        $this->criteriaApplicator = $criteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        // Resolve the page of customer ids first, then hydrate only those rows. The `total_spent`
        // and `connect` columns are correlated sub-selects over the (potentially huge) orders and
        // guest/connections tables; selecting them directly evaluates them for every matched
        // customer before the LIMIT is applied. Paginating on the ids first keeps those sub-selects
        // limited to the page that is actually displayed.
        $paginatedCustomerIds = $this->getCustomerQueryBuilder($searchCriteria)
            ->select('c.id_customer');

        // total_spent and connect are computed columns, so expose them on the id query only when
        // the grid is sorted by one of them, so its ORDER BY can reference the alias.
        if ('total_spent' === $searchCriteria->getOrderBy()) {
            $this->appendTotalSpentQuery($paginatedCustomerIds);
        } elseif ('connect' === $searchCriteria->getOrderBy()) {
            $this->appendLastVisitQuery($paginatedCustomerIds);
        }

        $this->applySorting($paginatedCustomerIds, $searchCriteria);
        $this->criteriaApplicator
            ->applyPagination($searchCriteria, $paginatedCustomerIds)
            ->applyDeterministicSorting($searchCriteria, $paginatedCustomerIds, 'c', 'id_customer')
        ;

        $searchQueryBuilder = $this->connection->createQueryBuilder()
            ->from('(' . $paginatedCustomerIds->getSQL() . ')', 'paginated')
            ->innerJoin('paginated', $this->dbPrefix . 'customer', 'c', 'c.id_customer = paginated.id_customer')
            ->select('c.id_customer, c.firstname, c.lastname, c.email, c.active, c.newsletter, c.optin')
            ->addSelect('c.date_add, gl.name as social_title, grl.name as default_group, s.name as shop_name, c.company');
        $this->applyJoins($searchQueryBuilder);

        $searchQueryBuilder->setParameters(
            $paginatedCustomerIds->getParameters(),
            $paginatedCustomerIds->getParameterTypes()
        );

        $this->appendTotalSpentQuery($searchQueryBuilder);
        $this->appendLastVisitQuery($searchQueryBuilder);
        // Re-apply the sorting: the join to the paginated subquery does not preserve its order.
        $this->applySorting($searchQueryBuilder, $searchCriteria);
        $this->criteriaApplicator->applyDeterministicSorting($searchCriteria, $searchQueryBuilder, 'c', 'id_customer');

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $countQueryBuilder = $this->getCustomerQueryBuilder($searchCriteria)
            ->select('COUNT(*)');

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getCustomerQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'customer', 'c')
            ->where('c.deleted = 0')
            ->andWhere('c.id_shop IN (:context_shop_ids)')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('context_lang_id', $this->contextLangId);

        $this->applyJoins($queryBuilder);
        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Add the joins shared by the id and hydration queries (gender, default group and shop names).
     *
     * @param QueryBuilder $queryBuilder
     */
    private function applyJoins(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->leftJoin(
                'c',
                $this->dbPrefix . 'gender_lang',
                'gl',
                'c.id_gender = gl.id_gender AND gl.id_lang = :context_lang_id'
            )
            ->leftJoin(
                'c',
                $this->dbPrefix . 'group_lang',
                'grl',
                'c.id_default_group = grl.id_group AND grl.id_lang = :context_lang_id'
            )
            ->leftJoin(
                'c',
                $this->dbPrefix . 'shop',
                's',
                'c.id_shop = s.id_shop'
            );
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function appendTotalSpentQuery(QueryBuilder $queryBuilder)
    {
        $totalSpentQueryBuilder = $this->connection->createQueryBuilder()
            ->select('SUM(total_paid_tax_incl / conversion_rate)')
            ->from($this->dbPrefix . 'orders', 'o')
            ->where('o.id_customer = c.id_customer')
            ->andWhere('o.id_shop IN (:context_shop_ids)')
            ->andWhere('o.valid = 1')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $queryBuilder->addSelect('(' . $totalSpentQueryBuilder->getSQL() . ') as total_spent');
    }

    /**
     * Append "last visit" column to customers query builder.
     *
     * @param QueryBuilder $queryBuilder
     */
    private function appendLastVisitQuery(QueryBuilder $queryBuilder)
    {
        $lastVisitQueryBuilder = $this->connection->createQueryBuilder()
            ->select('con.date_add')
            ->from($this->dbPrefix . 'guest', 'g')
            ->leftJoin('g', $this->dbPrefix . 'connections', 'con', 'con.id_guest = g.id_guest')
            ->where('g.id_customer = c.id_customer')
            ->orderBy('con.date_add', 'DESC')
            ->setMaxResults(1);

        $queryBuilder->addSelect('(' . $lastVisitQueryBuilder->getSQL() . ') as connect');
    }

    /**
     * Apply filters to customers query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFilters = [
            'id_customer',
            'social_title',
            'firstname',
            'lastname',
            'email',
            'default_group',
            'active',
            'newsletter',
            'optin',
            'date_add',
            'company',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['active', 'newsletter', 'optin', 'id_customer'])) {
                $qb->andWhere('c.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('social_title' === $filterName) {
                $qb->andWhere('gl.id_gender = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('default_group' === $filterName) {
                $qb->andWhere('grl.id_group = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('date_add' === $filterName) {
                if (isset($filterValue['from'])) {
                    $qb->andWhere('c.date_add >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                }

                if (isset($filterValue['to'])) {
                    $qb->andWhere('c.date_add <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }

            $qb->andWhere('c.`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
    }

    /**
     * Apply sorting so search query builder for customers.
     *
     * @param QueryBuilder $searchQueryBuilder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applySorting(QueryBuilder $searchQueryBuilder, SearchCriteriaInterface $searchCriteria)
    {
        switch ($searchCriteria->getOrderBy()) {
            case 'id_customer':
            case 'firstname':
            case 'lastname':
            case 'email':
            case 'date_add':
            case 'company':
            case 'active':
            case 'newsletter':
            case 'optin':
                $orderBy = 'c.' . $searchCriteria->getOrderBy();

                break;
            case 'social_title':
                $orderBy = 'gl.name';

                break;
            case 'default_group':
                $orderBy = 'grl.name';

                break;
            case 'connect':
            case 'total_spent':
                $orderBy = $searchCriteria->getOrderBy();

                break;
            default:
                return;
        }

        $searchQueryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
    }
}
