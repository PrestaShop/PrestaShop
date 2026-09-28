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
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;

/**
 * Builds search and count queries for the CSP violation log grid.
 *
 * Rows are always scoped to the shops carried by the search criteria's ShopConstraint, so a shop
 * never sees another shop's collected violations.
 */
final class CspLogQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const TEXT_FILTERS = ['directive', 'source'];

    private string $cspLogTable;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly ShopListResolverInterface $shopListResolver,
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->cspLogTable = $dbPrefix . 'csp_log';
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->buildBaseQuery($searchCriteria);
        $qb->select('c.id_csp_log', 'c.directive', 'c.source', 'c.document_uri', 'c.hits', 'c.date_add');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'c', 'id_csp_log')
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->buildBaseQuery($searchCriteria)->select('COUNT(c.id_csp_log)');
    }

    private function buildBaseQuery(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()->from($this->cspLogTable, 'c');

        $this->applyShopRestriction($qb, $searchCriteria);

        foreach ($searchCriteria->getFilters() as $filterName => $value) {
            if ('' === $value || null === $value) {
                continue;
            }

            if (in_array($filterName, self::TEXT_FILTERS, true)) {
                $qb->andWhere(sprintf('c.%s LIKE :%s', $filterName, $filterName));
                $qb->setParameter($filterName, '%' . $value . '%');
            }
        }

        return $qb;
    }

    private function applyShopRestriction(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        $shopConstraint = $searchCriteria instanceof ShopSearchCriteriaInterface ? $searchCriteria->getShopConstraint() : null;
        $shopIds = null !== $shopConstraint ? $this->shopListResolver->resolveShopIds($shopConstraint) : [];

        if ([] === $shopIds) {
            // Never fall back to every shop's log when the scope cannot be resolved.
            $qb->andWhere('1 = 0');

            return;
        }

        $qb->andWhere('c.id_shop IN (:shopIds)');
        $qb->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY);
    }
}
