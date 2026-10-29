<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Csp\CspSurfaceResolver;
use PrestaShop\PrestaShop\Core\Csp\CspWeakeningExpression;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds search and count queries for the CSP allow-list grid (the curated csp_rule rows), scoped to the
 * criteria's ShopConstraint and the selected surface (?context).
 */
final class CspRuleQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const TEXT_FILTERS = ['directive', 'source'];

    private readonly string $cspRuleTable;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly ShopListResolverInterface $shopListResolver,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->cspRuleTable = $dbPrefix . 'csp_rule';
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->cspRuleTable, 'c');
        $this->applyShopRestriction($qb, $searchCriteria);
        $this->applyFilters($qb, $searchCriteria->getFilters());

        $qb->select(
            'c.id_csp_rule',
            'c.directive',
            'c.source',
            'c.date_add',
            '(SELECT s.name FROM ' . $this->dbPrefix . 'shop s WHERE s.id_shop = c.id_shop LIMIT 1) AS shop_name',
            CspWeakeningExpression::sql('c.') . ' AS is_weakening'
        );
        CspWeakeningExpression::bindParameters($qb);

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'c', 'id_csp_rule')
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->cspRuleTable, 'c')
            ->select('COUNT(c.id_csp_rule)');

        $this->applyShopRestriction($qb, $searchCriteria);
        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        foreach ($filters as $filterName => $value) {
            if ('' === $value || null === $value) {
                continue;
            }

            if (in_array($filterName, self::TEXT_FILTERS, true)) {
                $qb->andWhere(sprintf('c.%s LIKE :%s', $filterName, $filterName));
                $qb->setParameter($filterName, '%' . $value . '%');
            }
        }
    }

    private function applyShopRestriction(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        $context = $this->resolveContext();
        $qb->andWhere('c.context = :cspContext')
            ->setParameter('cspContext', $context->value);

        // The back office is a single global surface stored under shop id 0; the storefront grid is
        // scoped to the shops in context. Admin rules never leak into the storefront view and vice versa.
        if (!$context->isPerShop()) {
            $qb->andWhere('c.id_shop = 0');

            return;
        }

        $shopConstraint = $searchCriteria instanceof ShopSearchCriteriaInterface ? $searchCriteria->getShopConstraint() : null;
        $shopIds = null !== $shopConstraint ? $this->shopListResolver->resolveShopIds($shopConstraint) : [];

        if ([] === $shopIds) {
            // Never fall back to every shop's allow-list when the scope cannot be resolved.
            $qb->andWhere('1 = 0');

            return;
        }

        $qb->andWhere('c.id_shop IN (:shopIds)');
        $qb->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER);
    }

    /** The surface the grid shows is selected by the ?context query param (default front). */
    private function resolveContext(): CspContext
    {
        return CspSurfaceResolver::fromRequestStack($this->requestStack);
    }
}
