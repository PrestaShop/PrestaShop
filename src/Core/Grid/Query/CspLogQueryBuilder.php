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
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspLogStatusFilter;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds search and count queries for the CSP violation log grid, scoped to the criteria's ShopConstraint.
 * A LEFT JOIN on csp_rule tells whether each reported source is already on the shop's allow-list.
 */
final class CspLogQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const TEXT_FILTERS = ['directive', 'source'];

    private readonly string $cspLogTable;

    private readonly string $cspRuleTable;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly ShopListResolverInterface $shopListResolver,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->cspLogTable = $dbPrefix . 'csp_log';
        $this->cspRuleTable = $dbPrefix . 'csp_rule';
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->cspLogTable, 'c');
        $this->joinRuleTable($qb);
        $this->applyShopRestriction($qb, $searchCriteria);
        $this->applyFilters($qb, $searchCriteria->getFilters());

        $qb->select(
            'c.id_csp_log',
            'c.directive',
            'c.source',
            'c.document_uri',
            'c.hits',
            'c.date_add',
            'r.id_csp_rule',
            // Correlated subquery (not a join) so the grid shows the shop per row in a multistore
            // scope without changing row counts; it reads the shop name for c.id_shop.
            '(SELECT s.name FROM ' . $this->dbPrefix . 'shop s WHERE s.id_shop = c.id_shop LIMIT 1) AS shop_name',
            'IF(r.id_csp_rule IS NULL, 0, 1) AS is_allowed',
            // Drives the bulk-action disabled_field: an un-allowed row has no rule to revoke.
            'IF(r.id_csp_rule IS NULL, 1, 0) AS is_not_allowed',
            // Weakening sources get the confirm-gated "Allow" action:
            // weakening keyword/wildcard, wildcard host, or broad scheme on script-/style-src.
            'IF(c.source IN (:weakeningSources)'
                . " OR c.source LIKE '%*%'"
                . ' OR (c.directive IN (:scriptStyleDirectives) AND c.source IN (:broadeningSchemes)), 1, 0) AS is_weakening'
        );
        $qb->setParameter('weakeningSources', CspSource::WEAKENING_KEYWORDS, ArrayParameterType::STRING);
        $qb->setParameter('scriptStyleDirectives', [CspDirective::SCRIPT_SRC->value, CspDirective::STYLE_SRC->value], ArrayParameterType::STRING);
        $qb->setParameter('broadeningSchemes', CspSource::BROADENING_SCHEMES, ArrayParameterType::STRING);

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'c', 'id_csp_log')
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $filters = $searchCriteria->getFilters();

        $qb = $this->connection->createQueryBuilder()
            ->from($this->cspLogTable, 'c')
            ->select('COUNT(c.id_csp_log)');

        // The allow-list join only affects the count when filtering by allowed/violations status,
        // so an unfiltered count (the common case) scans csp_log alone.
        if ($this->hasStatusFilter($filters)) {
            $this->joinRuleTable($qb);
        }
        $this->applyShopRestriction($qb, $searchCriteria);
        $this->applyFilters($qb, $filters);

        return $qb;
    }

    private function joinRuleTable(QueryBuilder $qb): void
    {
        $qb->leftJoin('c', $this->cspRuleTable, 'r', 'r.id_shop = c.id_shop AND r.context = c.context AND r.directive = c.directive AND r.source = c.source');
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

                continue;
            }

            if ('status' === $filterName) {
                $this->applyStatusFilter($qb, (string) $value);
            }
        }
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function hasStatusFilter(array $filters): bool
    {
        // isset() already excludes a null value, so only the empty string needs ruling out.
        return isset($filters['status']) && '' !== $filters['status'];
    }

    private function applyStatusFilter(QueryBuilder $qb, string $status): void
    {
        $statusFilter = CspLogStatusFilter::tryFrom($status);

        if (CspLogStatusFilter::ALLOWED === $statusFilter) {
            $qb->andWhere('r.id_csp_rule IS NOT NULL');
        } elseif (CspLogStatusFilter::VIOLATIONS === $statusFilter) {
            $qb->andWhere('r.id_csp_rule IS NULL');
        }
    }

    private function applyShopRestriction(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        $context = $this->resolveContext();
        $qb->andWhere('c.context = :cspContext')
            ->setParameter('cspContext', $context->value);

        // The back office is a single global surface stored under shop id 0; the storefront grid is
        // scoped to the shops in context. Admin rows never leak into the storefront view and vice versa.
        if (!$context->isPerShop()) {
            $qb->andWhere('c.id_shop = 0');

            return;
        }

        $shopConstraint = $searchCriteria instanceof ShopSearchCriteriaInterface ? $searchCriteria->getShopConstraint() : null;
        $shopIds = null !== $shopConstraint ? $this->shopListResolver->resolveShopIds($shopConstraint) : [];

        if ([] === $shopIds) {
            // Never fall back to every shop's log when the scope cannot be resolved.
            $qb->andWhere('1 = 0');

            return;
        }

        $qb->andWhere('c.id_shop IN (:shopIds)');
        $qb->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER);
    }

    /** The surface the grid shows is selected by the ?context query param (default front). */
    private function resolveContext(): CspContext
    {
        $request = $this->requestStack->getCurrentRequest();

        return null !== $request && 'admin' === $request->query->get('context') ? CspContext::ADMIN : CspContext::FRONT;
    }
}
