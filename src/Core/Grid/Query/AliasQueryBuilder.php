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

class AliasQueryBuilder extends AbstractDoctrineQueryBuilder
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
    public function __construct(Connection $connection, $dbPrefix, DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator)
    {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $builder = $this->getAliasQueryBuilder($searchCriteria);

        $builder->select('DISTINCT a.search');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $builder)
            ->applyPagination($searchCriteria, $builder);

        return $builder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getAliasQueryBuilder($searchCriteria)->select('COUNT(DISTINCT(a.search))');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getAliasQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $builder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'alias', 'a')
        ;

        $this->applyFilters($builder, $searchCriteria);

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $searchCriteria): void
    {
        $filtersMap = [
            'alias' => 'a.alias',
            'search' => 'a.search',
        ];

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (!array_key_exists($filterName, $filtersMap)) {
                continue;
            }

            $dbColumn = $filtersMap[$filterName];

            $builder
                ->andWhere($dbColumn . ' LIKE :' . $filterName)
                ->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
