<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class SearchEngineQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getSearchEngineQueryBuilder($searchCriteria->getFilters())
            ->select('se.id_search_engine, se.server, se.getvar');

        // Create new search criteria if filter is query_key
        if ($searchCriteria->getOrderBy() === 'query_key') {
            $searchCriteria = new SearchCriteria(
                $searchCriteria->getFilters(),
                'getvar',
                $searchCriteria->getOrderWay(),
                $searchCriteria->getOffset(),
                $searchCriteria->getLimit()
            );
        }

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getSearchEngineQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT se.id_search_engine)');
    }

    /**
     * Gets query builder with the common sql used for displaying search engines list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getSearchEngineQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'search_engine', 'se');

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $allowedFilters = [
            'id_search_engine' => 'se.id_search_engine',
            'server' => 'se.server',
            'query_key' => 'se.getvar',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!array_key_exists($filterName, $allowedFilters)) {
                continue;
            }

            if ($filterName === 'id_search_engine') {
                $qb->andWhere($allowedFilters[$filterName] . ' = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            $qb->andWhere($allowedFilters[$filterName] . ' LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
