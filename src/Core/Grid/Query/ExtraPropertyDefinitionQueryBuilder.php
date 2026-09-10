<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;

/**
 * Builds search and count queries for the extra property definition grid.
 *
 * Queries the extra_property_definition registry table with text (LIKE) filters
 * on entity_name, module_name, and property_name, and exact-match filters on
 * type and scope.
 *
 * Rows are additionally restricted to the definitions AVAILABLE in the current shop
 * context (single shop: available on that shop; shop group: on at least one of its
 * shops; all shops: everything). The SQL mirrors the PHP availability rules of
 * ExtraPropertyDefinition::isAvailableForShops() — keep both in lockstep.
 */
final class ExtraPropertyDefinitionQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var string
     */
    protected string $definitionTable;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param ShopListResolverInterface $shopListResolver
     * @param FeatureInterface $multistoreFeature
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        protected readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        protected readonly ShopListResolverInterface $shopListResolver,
        protected readonly FeatureInterface $multistoreFeature,
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->definitionTable = $dbPrefix . 'extra_property_definition';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->buildBaseQuery($searchCriteria->getFilters());
        $this->applyShopContextRestriction($qb, $searchCriteria);

        $qb->select(
            'epd.id_extra_property_definition',
            'epd.entity_name',
            'epd.module_name',
            'epd.property_name',
            'epd.type',
            'epd.scope',
            'epd.sql_index',
            'epd.display_front'
        );

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'epd', 'id_extra_property_definition')
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->buildBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(epd.id_extra_property_definition)');
        $this->applyShopContextRestriction($qb, $searchCriteria);

        return $qb;
    }

    /**
     * Restricts the rows to the definitions available for the shop scope carried by the
     * search criteria. No-op in all-shops context (everything is listed there, that is
     * where the whole registry is managed) and when multistore is not USED (feature
     * disabled, or a single shop — where a restriction can never usefully exclude
     * anything; stale association rows are ignored). isUsed() is the single criterion
     * shared by every extra-property multistore gate — filtering layer and UI alike
     * (ExtraPropertyDefinitionShopFilter, the grid column, the form fields).
     */
    protected function applyShopContextRestriction(QueryBuilder $qb, SearchCriteriaInterface $searchCriteria): void
    {
        if (!$this->multistoreFeature->isUsed()) {
            return;
        }

        $shopConstraint = $searchCriteria instanceof ShopSearchCriteriaInterface ? $searchCriteria->getShopConstraint() : null;
        if (null === $shopConstraint || $shopConstraint->forAllShops()) {
            return;
        }

        $contextShopIds = $this->shopListResolver->resolveShopIds($shopConstraint);
        if ([] === $contextShopIds) {
            return;
        }

        // Table names cannot be bound parameters (SQL identifiers), so they are interpolated
        // through clearly-named variables instead; :contextShopIds is the only real parameter.
        $definitionShopTable = $this->dbPrefix . 'extra_property_definition_shop';
        $moduleTable = $this->dbPrefix . 'module';
        $moduleShopTable = $this->dbPrefix . 'module_shop';

        // Availability rules, in SQL (mirror of ExtraPropertyDefinition::isAvailableForShops()):
        //  1. explicit restriction intersecting the scope, or
        //  2. no explicit restriction AND (core-owned, or the owning module is enabled on a
        //     shop of the scope, or the module has no module_shop row at all — the degenerate
        //     "registered before being enabled" case, treated as unrestricted).
        $qb->andWhere(
            "(
                EXISTS (SELECT 1 FROM {$definitionShopTable} epds
                    WHERE epds.id_extra_property_definition = epd.id_extra_property_definition
                    AND epds.id_shop IN (:contextShopIds))
                OR (
                    NOT EXISTS (SELECT 1 FROM {$definitionShopTable} epds2
                        WHERE epds2.id_extra_property_definition = epd.id_extra_property_definition)
                    AND (
                        epd.module_name IS NULL
                        OR EXISTS (SELECT 1 FROM {$moduleTable} m INNER JOIN {$moduleShopTable} ms ON ms.id_module = m.id_module
                            WHERE m.name = epd.module_name AND ms.id_shop IN (:contextShopIds))
                        OR NOT EXISTS (SELECT 1 FROM {$moduleTable} m2 INNER JOIN {$moduleShopTable} ms2 ON ms2.id_module = m2.id_module
                            WHERE m2.name = epd.module_name)
                    )
                )
            )"
        );
        $qb->setParameter('contextShopIds', $contextShopIds, Connection::PARAM_INT_ARRAY);
    }

    /**
     * Builds the base query with FROM and WHERE clauses applied from active filters.
     *
     * @param array<string, mixed> $filters
     *
     * @return QueryBuilder
     */
    protected function buildBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->definitionTable, 'epd');

        foreach ($filters as $filterName => $value) {
            if ('' === $value || null === $value) {
                continue;
            }

            // Exact-match filters for ENUM columns
            if (in_array($filterName, ['type', 'scope'], true)) {
                $qb->andWhere(sprintf('epd.%s = :%s', $filterName, $filterName));
                $qb->setParameter($filterName, $value);

                continue;
            }

            // LIKE filter for text columns
            if (in_array($filterName, ['entity_name', 'module_name', 'property_name'], true)) {
                $qb->andWhere(sprintf('epd.%s LIKE :%s', $filterName, $filterName));
                $qb->setParameter($filterName, '%' . $value . '%');

                continue;
            }
        }

        return $qb;
    }
}
