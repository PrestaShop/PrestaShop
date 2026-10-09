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
use PrestaShop\PrestaShop\Core\Csp\CspWeakeningExpression;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds search and count queries for the CSP violation grid (one row per source per page), scoped to the
 * criteria's ShopConstraint. The grid shows only violations not yet on the allow-list: a source that has
 * been allowed (a matching csp_rule exists) is excluded, since it has moved to the allow-list grid.
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
        $this->excludeAllowed($qb);
        $this->applyShopRestriction($qb, $searchCriteria);
        $this->applyFilters($qb, $searchCriteria->getFilters());

        $qb->select(
            'c.id_csp_log',
            'c.directive',
            'c.source',
            'c.document_uri',
            // A short sample of the offending inline code and where it lives, so the merchant can see what
            // triggered the source. "source_file:line" when both are known, just the file otherwise.
            'c.sample',
            "IF(c.source_file IS NULL, NULL, CONCAT(c.source_file, IFNULL(CONCAT(':', c.line_number), ''))) AS source_location",
            'c.hits',
            'c.date_add',
            // Correlated subquery (not a join) so the grid shows the shop per row in a multistore
            // scope without changing row counts; it reads the shop name for c.id_shop.
            '(SELECT s.name FROM ' . $this->dbPrefix . 'shop s WHERE s.id_shop = c.id_shop LIMIT 1) AS shop_name',
            // Weakening sources get the confirm-gated "Allow" action.
            CspWeakeningExpression::sql('c.') . ' AS is_weakening'
        );
        CspWeakeningExpression::bindParameters($qb);

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'c', 'id_csp_log')
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->cspLogTable, 'c')
            ->select('COUNT(c.id_csp_log)');

        $this->excludeAllowed($qb);
        $this->applyShopRestriction($qb, $searchCriteria);
        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    /** Keeps only violations whose source is not yet on the allow-list (no matching csp_rule). */
    private function excludeAllowed(QueryBuilder $qb): void
    {
        $qb->andWhere(
            'NOT EXISTS (SELECT 1 FROM ' . $this->cspRuleTable . ' r'
            . ' WHERE r.id_shop = c.id_shop AND r.context = c.context AND r.directive = c.directive AND r.source = c.source)'
        );
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
