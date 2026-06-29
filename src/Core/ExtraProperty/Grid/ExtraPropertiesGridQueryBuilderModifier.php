<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Grid;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyValueCaster;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Adds JOIN/SELECT/FILTER clauses for extra properties in BO Symfony grids.
 *
 * Cardinality invariant: every LEFT JOIN added here covers the FULL primary key of its
 * extra table ({e}_extra: id_e; {e}_extra_lang: id_e + id_lang + id_shop; {e}_extra_shop:
 * id_e + id_shop), so each join matches at most one row per existing grid row. Joins
 * enrich rows 1:1 and can never multiply them — pagination and COUNT stay correct without
 * any GROUP BY (which is forbidden in this service).
 *
 * The shop pin of lang/shop joins is resolved per builder, in order: the base
 * {entity}_lang/{entity}_shop join alias when that builder has one, the builder's own
 * :shopId parameter, then ShopContext (single-shop constraint → its id, otherwise the
 * current shop id — same rule as the toggle column in ExtraPropertiesGridDefinitionModifier).
 *
 * Joins and parameters are built independently for the search and count builders: their
 * query shapes usually differ (count builders rarely carry the base lang/shop joins), so
 * an alias resolved on one builder is never reused on the other.
 *
 * This modifier assumes:
 * - grid id matches the entity table name (e.g. "product")
 * - registry structure and *_extra tables are coherent (no runtime checks for performance)
 */
class ExtraPropertiesGridQueryBuilderModifier
{
    private const EXTRA_ENTITY_ALIAS = 'extra_entity';
    private const EXTRA_LANG_ALIAS = 'extra_lang';
    private const EXTRA_SHOP_ALIAS = 'extra_shop';

    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        protected readonly string $dbPrefix,
        protected readonly LanguageContext $languageContext,
        protected readonly ShopContext $shopContext,
    ) {
    }

    public function apply(
        QueryBuilder $searchQueryBuilder,
        QueryBuilder $countQueryBuilder,
        SearchCriteriaInterface $searchCriteria,
        string $gridId,
    ): void {
        $definitions = $this->repository->getAllDefinitions()->filterByGrid($gridId);
        if ($definitions->isEmpty()) {
            return;
        }

        $entityName = $definitions->first()->getEntityName();
        $primaryKey = $definitions->first()->getPrimaryKeyName();

        // Resolve the main table alias in EACH builder: filters apply to both builders, so
        // when either one cannot take the joins the whole scope is skipped for both
        // (otherwise the count would diverge from the page).
        $searchMainAlias = $this->resolveMainAlias($searchQueryBuilder, $gridId, $entityName);
        $countMainAlias = $this->resolveMainAlias($countQueryBuilder, $gridId, $entityName);
        if (null === $searchMainAlias || null === $countMainAlias) {
            return;
        }

        // Search builder first: applySelectsAndFilters() adds the SELECTs to $builders[0] only.
        $builders = [
            [$searchQueryBuilder, $searchMainAlias],
            [$countQueryBuilder, $countMainAlias],
        ];

        $this->applyEntityScope($builders, $searchCriteria, $primaryKey, $definitions->filterByScope(ExtraPropertyScope::COMMON));
        $this->applyLangScope($builders, $searchCriteria, $entityName, $primaryKey, $definitions->filterByScope(ExtraPropertyScope::LANG));
        $this->applyShopScope($builders, $searchCriteria, $entityName, $primaryKey, $definitions->filterByScope(ExtraPropertyScope::SHOP));
    }

    /**
     * Casts the extra property columns of fetched grid records to their declared PHP types.
     *
     * Counterpart of apply(): apply() only shapes the query (JOIN/SELECT/WHERE), so values come
     * out of the DB as raw strings; this method is called after the query has run (see
     * DoctrineGridDataFactory) and casts each extra column in place via ExtraPropertyValueCaster.
     * Grid lang values are single-language scalars (joined on one id_lang), so the scalar cast
     * applies to every scope.
     *
     * @param array<int, array<string, mixed>> $records Rows fetched by the grid search query
     *
     * @return array<int, array<string, mixed>> Same rows with typed extra property values
     */
    public function castExtraProperties(array $records, string $gridId): array
    {
        $definitions = $this->repository->getAllDefinitions()->filterByGrid($gridId);
        if ($definitions->isEmpty()) {
            return $records;
        }

        foreach ($records as &$record) {
            foreach ($definitions as $definition) {
                $selectAlias = $definition->getFieldName();
                if (array_key_exists($selectAlias, $record)) {
                    $record[$selectAlias] = ExtraPropertyValueCaster::castFromDb(
                        $definition->getType(),
                        $record[$selectAlias],
                        $definition->isNullable()
                    );
                }
            }
        }

        return $records;
    }

    /**
     * @param array<int, array{0: QueryBuilder, 1: string}> $builders [[builder, mainAlias], ...], search builder first
     * @param ExtraPropertyDefinitionCollection $definitions
     */
    protected function applyEntityScope(
        array $builders,
        SearchCriteriaInterface $criteria,
        string $primaryKey,
        ExtraPropertyDefinitionCollection $definitions,
    ): void {
        if ($definitions->isEmpty()) {
            return;
        }

        $extraTable = $this->dbPrefix . $definitions->first()->getExtraTableName();

        foreach ($builders as [$qb, $mainAlias]) {
            // PK-complete: {e}_extra PK is (id_e) — at most one row per grid row.
            $this->ensureLeftJoin($qb, $mainAlias, $extraTable, self::EXTRA_ENTITY_ALIAS, sprintf(
                '%s.`%s` = %s.`%s`',
                self::EXTRA_ENTITY_ALIAS,
                $primaryKey,
                $mainAlias,
                $primaryKey
            ));
        }

        $this->applySelectsAndFilters($builders, $criteria, self::EXTRA_ENTITY_ALIAS, $definitions);
    }

    /**
     * @param array<int, array{0: QueryBuilder, 1: string}> $builders [[builder, mainAlias], ...], search builder first
     * @param ExtraPropertyDefinitionCollection $definitions
     */
    protected function applyLangScope(
        array $builders,
        SearchCriteriaInterface $criteria,
        string $entityName,
        string $primaryKey,
        ExtraPropertyDefinitionCollection $definitions,
    ): void {
        if ($definitions->isEmpty()) {
            return;
        }

        $extraTable = $this->dbPrefix . $definitions->first()->getExtraTableName();
        $baseLangTable = $this->dbPrefix . $entityName . '_lang';

        foreach ($builders as [$qb, $mainAlias]) {
            [$langAlias, $langJoinCondition] = $this->findJoinedTableAliasAndCondition($qb, $baseLangTable);

            // PK-complete: {e}_extra_lang PK is (id_e, id_lang, id_shop) — all three are
            // always pinned so the join can never multiply rows in multistore.
            $parameters = [];
            if (null !== $langAlias) {
                $fromAlias = $langAlias;
                $joinParts = [
                    sprintf('%s.`%s` = %s.`%s`', self::EXTRA_LANG_ALIAS, $primaryKey, $langAlias, $primaryKey),
                    sprintf('%s.`id_lang` = %s.`id_lang`', self::EXTRA_LANG_ALIAS, $langAlias),
                ];
                if (null !== $langJoinCondition && $this->joinConditionMentionsShopId($langAlias, $langJoinCondition)) {
                    $joinParts[] = sprintf('%s.`id_shop` = %s.`id_shop`', self::EXTRA_LANG_ALIAS, $langAlias);
                } else {
                    $joinParts[] = sprintf('%s.`id_shop` = :extraLangShopId', self::EXTRA_LANG_ALIAS);
                    $parameters['extraLangShopId'] = $this->resolveShopId($qb);
                }
            } else {
                // Fallback when no base lang join exists in this builder: context language.
                $fromAlias = $mainAlias;
                $joinParts = [
                    sprintf('%s.`%s` = %s.`%s`', self::EXTRA_LANG_ALIAS, $primaryKey, $mainAlias, $primaryKey),
                    sprintf('%s.`id_lang` = :extraLangId', self::EXTRA_LANG_ALIAS),
                    sprintf('%s.`id_shop` = :extraLangShopId', self::EXTRA_LANG_ALIAS),
                ];
                $parameters['extraLangId'] = $this->languageContext->getId();
                $parameters['extraLangShopId'] = $this->resolveShopId($qb);
            }

            $this->ensureLeftJoin($qb, $fromAlias, $extraTable, self::EXTRA_LANG_ALIAS, implode(' AND ', $joinParts), $parameters);
        }

        $this->applySelectsAndFilters($builders, $criteria, self::EXTRA_LANG_ALIAS, $definitions);
    }

    /**
     * @param array<int, array{0: QueryBuilder, 1: string}> $builders [[builder, mainAlias], ...], search builder first
     * @param ExtraPropertyDefinitionCollection $definitions
     */
    protected function applyShopScope(
        array $builders,
        SearchCriteriaInterface $criteria,
        string $entityName,
        string $primaryKey,
        ExtraPropertyDefinitionCollection $definitions,
    ): void {
        if ($definitions->isEmpty()) {
            return;
        }

        $extraTable = $this->dbPrefix . $definitions->first()->getExtraTableName();
        $baseShopTable = $this->dbPrefix . $entityName . '_shop';

        foreach ($builders as [$qb, $mainAlias]) {
            [$shopAlias] = $this->findJoinedTableAliasAndCondition($qb, $baseShopTable);

            // PK-complete: {e}_extra_shop PK is (id_e, id_shop) — at most one row per
            // (entity, shop) pair of this builder.
            if (null !== $shopAlias) {
                $this->ensureLeftJoin($qb, $shopAlias, $extraTable, self::EXTRA_SHOP_ALIAS, sprintf(
                    '%s.`%s` = %s.`%s` AND %s.`id_shop` = %s.`id_shop`',
                    self::EXTRA_SHOP_ALIAS,
                    $primaryKey,
                    $shopAlias,
                    $primaryKey,
                    self::EXTRA_SHOP_ALIAS,
                    $shopAlias
                ));
            } else {
                // Fallback when no base shop join exists in this builder: pin on the
                // builder's :shopId param or the context shop.
                $this->ensureLeftJoin($qb, $mainAlias, $extraTable, self::EXTRA_SHOP_ALIAS, sprintf(
                    '%s.`%s` = %s.`%s` AND %s.`id_shop` = :extraShopId',
                    self::EXTRA_SHOP_ALIAS,
                    $primaryKey,
                    $mainAlias,
                    $primaryKey,
                    self::EXTRA_SHOP_ALIAS
                ), ['extraShopId' => $this->resolveShopId($qb)]);
            }
        }

        $this->applySelectsAndFilters($builders, $criteria, self::EXTRA_SHOP_ALIAS, $definitions);
    }

    /**
     * Resolves the shop id used to pin lang/shop extra joins when no base join carries it:
     * the builder's own :shopId parameter when set, otherwise the context shop (single-shop
     * constraint → its id; all-shops/group context → the current shop id, mirroring the
     * toggle column rule in ExtraPropertiesGridDefinitionModifier).
     */
    protected function resolveShopId(QueryBuilder $qb): int
    {
        $shopIdParam = $qb->getParameters()['shopId'] ?? null;
        if (is_numeric($shopIdParam) && (int) $shopIdParam > 0) {
            return (int) $shopIdParam;
        }

        $shopConstraint = $this->shopContext->getShopConstraint();

        return $shopConstraint->isSingleShopContext()
            ? $shopConstraint->getShopId()->getValue()
            : $this->shopContext->getId();
    }

    /**
     * Adds the SELECT aliases (search builder only) and the filter WHEREs (every builder,
     * so the count stays consistent with the page).
     *
     * @param array<int, array{0: QueryBuilder, 1: string}> $builders [[builder, mainAlias], ...], search builder first
     * @param ExtraPropertyDefinitionCollection $definitions
     */
    protected function applySelectsAndFilters(
        array $builders,
        SearchCriteriaInterface $criteria,
        string $joinAlias,
        ExtraPropertyDefinitionCollection $definitions,
    ): void {
        [$searchQb] = $builders[0];
        $filters = $criteria->getFilters();

        foreach ($definitions as $definition) {
            $storageColumn = $definition->getStorageColumnName();

            $selectAlias = $definition->getFieldName();
            $searchQb->addSelect(sprintf('%s.`%s` AS `%s`', $joinAlias, $storageColumn, $selectAlias));

            if (!array_key_exists($selectAlias, $filters)) {
                continue;
            }

            $filterValue = $filters[$selectAlias];
            if (null === $filterValue || '' === $filterValue) {
                continue;
            }

            $paramName = $this->buildFilterParamName($selectAlias);
            $isBoolean = CheckboxType::class === $definition->getFormFieldType();

            foreach ($builders as [$qb]) {
                if ($isBoolean) {
                    $this->applyWhereEquals($qb, $joinAlias, $storageColumn, $paramName, $filterValue);
                } else {
                    $this->applyWhereLike($qb, $joinAlias, $storageColumn, $paramName, $filterValue);
                }
            }
        }
    }

    protected function applyWhereEquals(
        QueryBuilder $qb,
        string $alias,
        string $column,
        string $paramName,
        mixed $value,
    ): void {
        $qb->andWhere(sprintf('%s.`%s` = :%s', $alias, $column, $paramName))
            ->setParameter($paramName, $value);
    }

    protected function applyWhereLike(
        QueryBuilder $qb,
        string $alias,
        string $column,
        string $paramName,
        mixed $value,
    ): void {
        $qb->andWhere(sprintf('%s.`%s` LIKE :%s', $alias, $column, $paramName))
            ->setParameter($paramName, '%' . (string) $value . '%');
    }

    protected function buildFilterParamName(string $filterName): string
    {
        return 'extra_filter_' . substr(sha1($filterName), 0, 10);
    }

    /**
     * Adds a LEFT JOIN to one builder unless the table is already joined there; the
     * condition's parameters are only bound when the join is actually added.
     *
     * @param array<string, mixed> $parameters Named parameters used by $condition
     */
    protected function ensureLeftJoin(
        QueryBuilder $qb,
        string $fromAlias,
        string $joinTable,
        string $joinAlias,
        string $condition,
        array $parameters = [],
    ): void {
        if (null !== $this->findJoinedTableAliasAndCondition($qb, $joinTable)[0]) {
            return;
        }

        $qb->leftJoin($fromAlias, $joinTable, $joinAlias, $condition);
        foreach ($parameters as $name => $value) {
            $qb->setParameter($name, $value);
        }
    }

    protected function resolveMainAlias(QueryBuilder $qb, string $gridId, string $entityName): ?string
    {
        $fromParts = $qb->getQueryPart('from');
        if (!is_array($fromParts)) {
            return null;
        }

        $mainTables = array_values(array_unique([
            $this->dbPrefix . strtolower($gridId),
            $this->dbPrefix . strtolower($entityName),
        ]));
        foreach ($fromParts as $from) {
            if (!is_array($from)) {
                continue;
            }
            $table = $from['table'] ?? null;
            $alias = $from['alias'] ?? null;
            if (is_string($table) && in_array(strtolower($table), $mainTables, true) && is_string($alias) && '' !== $alias) {
                return $alias;
            }
        }

        return null;
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    protected function findJoinedTableAliasAndCondition(QueryBuilder $qb, string $tableName): array
    {
        $joinParts = $qb->getQueryPart('join');
        if (!is_array($joinParts)) {
            return [null, null];
        }

        foreach ($joinParts as $joinsByFromAlias) {
            if (!is_array($joinsByFromAlias)) {
                continue;
            }

            foreach ($joinsByFromAlias as $join) {
                if (!is_array($join)) {
                    continue;
                }
                $joinTable = $join['joinTable'] ?? $join['table'] ?? null;
                if ($tableName !== $joinTable) {
                    continue;
                }

                $alias = $join['joinAlias'] ?? $join['alias'] ?? null;
                $condition = $join['joinCondition'] ?? $join['condition'] ?? null;

                return [is_string($alias) ? $alias : null, is_string($condition) ? $condition : null];
            }
        }

        return [null, null];
    }

    protected function joinConditionMentionsShopId(string $langAlias, string $joinCondition): bool
    {
        // Detect patterns like "pl.`id_shop`" or "pl.id_shop"
        return (bool) preg_match('/\b' . preg_quote($langAlias, '/') . '\.(?:`)?id_shop(?:`)?\b/', $joinCondition);
    }
}
