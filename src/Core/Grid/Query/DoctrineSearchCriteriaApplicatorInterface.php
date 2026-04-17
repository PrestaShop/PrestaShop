<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Interface DoctrineSearchCriteriaApplicatorInterface contract for doctrine query builder applicator.
 */
interface DoctrineSearchCriteriaApplicatorInterface
{
    /**
     * Apply pagination on query builder.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param QueryBuilder $queryBuilder
     *
     * @return self
     */
    public function applyPagination(SearchCriteriaInterface $searchCriteria, QueryBuilder $queryBuilder);

    /**
     * Apply sorting on query builder.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param QueryBuilder $queryBuilder
     *
     * @return self
     */
    public function applySorting(SearchCriteriaInterface $searchCriteria, QueryBuilder $queryBuilder);

    /**
     * Apply deterministic sorting (stable order) on query builder.
     * Useful when the requested sorting may lead to non-deterministic results
     * (e.g. same values across many rows), so it appends a tie-breaker.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param QueryBuilder $queryBuilder
     * @param string $alias The root alias used in the query (e.g. "a")
     * @param string $primaryKey The primary key field name (e.g. "id")
     *
     * @return self
     */
    public function applyDeterministicSorting(SearchCriteriaInterface $searchCriteria, QueryBuilder $queryBuilder, string $alias, string $primaryKey);
}
