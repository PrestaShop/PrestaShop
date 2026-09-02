<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class DoctrineSearchCriteriaApplicator applies search criteria to doctrine query builder.
 */
final class DoctrineSearchCriteriaApplicator implements DoctrineSearchCriteriaApplicatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyPagination(SearchCriteriaInterface $searchCriteria, QueryBuilder $queryBuilder)
    {
        if (null !== $searchCriteria->getLimit()) {
            $queryBuilder->setMaxResults($searchCriteria->getLimit());
        }

        if (null !== $searchCriteria->getOffset()) {
            $queryBuilder->setFirstResult($searchCriteria->getOffset());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function applySorting(SearchCriteriaInterface $searchCriteria, QueryBuilder $queryBuilder)
    {
        if (null !== $searchCriteria->getOrderBy() && null !== $searchCriteria->getOrderWay()) {
            $queryBuilder->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function applyDeterministicSorting(
        SearchCriteriaInterface $searchCriteria,
        QueryBuilder $queryBuilder,
        string $alias,
        string $primaryKey
    ) {
        if ($searchCriteria->getOrderBy() !== $primaryKey) {
            $queryBuilder->addOrderBy(
                sprintf('%s.`%s`', $alias, $primaryKey),
                $searchCriteria->getOrderWay()
            );
        }

        return $this;
    }
}
