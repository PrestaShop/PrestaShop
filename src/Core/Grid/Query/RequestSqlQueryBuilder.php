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
 * Class RequestSqlQueryBuilder builds search & count queries for RequestSql grid.
 */
final class RequestSqlQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var string
     */
    private $requestSqlTable;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        $dbPrefix
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->requestSqlTable = $dbPrefix . 'request_sql';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(?SearchCriteriaInterface $searchCriteria = null): QueryBuilder
    {
        $searchQueryBuilder = $this->buildQueryBySearchCriteria($searchCriteria);

        return $searchQueryBuilder
            ->select('rs.*')
            ->orderBy(sprintf('`%s`', $searchCriteria->getOrderBy()), $searchCriteria->getOrderWay())
            ->setFirstResult($searchCriteria->getOffset() ?? 0)
            ->setMaxResults($searchCriteria->getLimit());
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(?SearchCriteriaInterface $searchCriteria = null)
    {
        $countQueryBuilder = $this->buildQueryBySearchCriteria($searchCriteria);
        $countQueryBuilder->select('COUNT(rs.id_request_sql)');

        return $countQueryBuilder;
    }

    /**
     * Build partial query by search criteria.
     *
     * @param SearchCriteriaInterface $criteria
     *
     * @return QueryBuilder
     */
    private function buildQueryBySearchCriteria(SearchCriteriaInterface $criteria)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->requestSqlTable, 'rs');

        foreach ($criteria->getFilters() as $filterName => $value) {
            if (empty($value)) {
                continue;
            }

            if ('id_request_sql' === $filterName) {
                $qb->andWhere('rs.id_request_sql = :id_request_sql');
                $qb->setParameter('id_request_sql', $value);

                continue;
            }

            $qb->andWhere("`$filterName` LIKE :$filterName");
            $qb->setParameter($filterName, '%' . $value . '%');
        }

        return $qb;
    }
}
