<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Value;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use Throwable;

/**
 * Writes extra property values into the *_extra / *_extra_lang / *_extra_shop tables.
 *
 * Callers pass values grouped the same way the reader returns them
 * ([moduleKey => [propertyName => value]]); the writer resolves each property's
 * definition and routes the value to the table matching its scope. Storage column
 * names never leave the storage layer.
 *
 * All writes use UPSERT (INSERT … ON DUPLICATE KEY UPDATE) to handle the case where
 * a row may or may not already exist.
 *
 * Per-shop values (SHOP scope, and LANG scope on multilang-multishop entities) follow the
 * ShopConstraint the same way native ObjectModel fields follow the legacy shop context:
 * a non-single constraint (shop group, all shops, collection) fans out to one row per shop
 * in its scope, so a broad edit updates every covered shop instead of being dropped.
 * SHOP-scope rows additionally follow the native association rule — broad scopes only
 * refresh shops the entity is associated with, explicitly named shops always get their
 * row — while LANG rows cover the full scope like native lang-multishop writes do.
 * Fan-out writes are batched into one multi-row UPSERT per table.
 *
 * DEFINITION-level shop availability (extra_property_definition_shop + module fallback,
 * see ExtraPropertyDefinition::isAvailableForShops()) is enforced on top of all of this:
 * a definition not available anywhere in the constraint's scope is skipped entirely, and
 * per-shop rows (SHOP scope, multishop LANG) only fan out to the shops each definition is
 * available for — unlike the entity association rule above, this applies to LANG rows too
 * (definition availability is a different axis: the reader never surfaces a value on a
 * shop the definition does not exist for, so such rows would be unreadable garbage).
 */
class ExtraPropertyWriter implements ExtraPropertyWriterInterface
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $prefix,
        protected readonly ExtraPropertyDefinitionRepositoryInterface $definitionRepository,
        protected readonly ShopListResolverInterface $shopListResolver,
        protected readonly ExtraPropertyDefinitionShopFilterInterface $definitionShopFilter,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function writeAll(
        string $entityName,
        string $primaryKeyName,
        int $entityId,
        array $valuesByModule,
        ShopConstraint $shopConstraint,
        ?int $defaultLangId = null,
    ): void {
        if (empty($valuesByModule)) {
            return;
        }

        $definitions = $this->definitionRepository->getAllDefinitions()->filterByEntity($entityName);
        if ($definitions->isEmpty()) {
            return;
        }

        // One row per shop covered by the constraint — native ObjectModel parity
        // (a group / all-shops edit updates every shop in scope).
        $shopIds = $this->shopListResolver->resolveShopIds($shopConstraint);

        // Definitions not available anywhere in the scope are skipped entirely — their
        // values are silently dropped, mirroring the reader which never surfaces them.
        $definitions = $this->definitionShopFilter->filterByShopIds($definitions, $shopIds);
        if ($definitions->isEmpty()) {
            return;
        }

        $entityValues = [];
        // Per-shop rows fan out to each DEFINITION's available subset of the scope, so
        // definitions restricted to different shops cannot share one multi-row statement:
        // buckets group definitions by identical effective shop set (a single bucket in
        // the common unrestricted case).
        $langBuckets = [];
        $shopBuckets = [];
        $entityTableName = null;
        $langTableName = null;
        $langIsMultiShop = null;
        $shopTableName = null;

        foreach ($definitions as $definition) {
            $moduleKey = $definition->getNormalizedModuleKey();
            $propertyName = $definition->getPropertyName();
            if (!isset($valuesByModule[$moduleKey])
                || !is_array($valuesByModule[$moduleKey])
                || !array_key_exists($propertyName, $valuesByModule[$moduleKey])
            ) {
                continue;
            }

            $value = $valuesByModule[$moduleKey][$propertyName];
            $isNullable = $definition->isNullable();
            // NULL is a legitimate value for nullable columns; for NOT NULL columns it is
            // skipped so the SQL default applies on first insert.
            if (null === $value && !$isNullable) {
                continue;
            }

            $columnName = $definition->getStorageColumnName();

            if (ExtraPropertyScope::LANG === $definition->getScope()) {
                $langTableName ??= $definition->getExtraTableName();
                $langIsMultiShop ??= $definition->isMultiShop();
                // The entity's lang table may have no id_shop column: then one row per
                // language, shared by all shops (single 'shared' bucket).
                $effectiveShopIds = $langIsMultiShop
                    ? $this->definitionShopFilter->getAvailableShopIds($definition, $shopIds)
                    : null;
                $bucketKey = null === $effectiveShopIds ? 'shared' : implode(',', $effectiveShopIds);
                $langBuckets[$bucketKey]['shopIds'] = $effectiveShopIds;
                if (is_array($value)) {
                    // Multilang array: one entry per language.
                    foreach ($value as $langId => $langValue) {
                        if ((int) $langId <= 0 || (null === $langValue && !$isNullable)) {
                            continue;
                        }
                        $langBuckets[$bucketKey]['valuesByLang'][(int) $langId][$columnName] = $langValue;
                    }
                } elseif (null !== $defaultLangId && $defaultLangId > 0) {
                    // Scalar lang value: written for the caller-provided language only.
                    $langBuckets[$bucketKey]['valuesByLang'][$defaultLangId][$columnName] = $value;
                }
            } elseif (ExtraPropertyScope::SHOP === $definition->getScope()) {
                $shopTableName ??= $definition->getExtraTableName();
                $effectiveShopIds = $this->definitionShopFilter->getAvailableShopIds($definition, $shopIds);
                $bucketKey = implode(',', $effectiveShopIds);
                $shopBuckets[$bucketKey]['shopIds'] = $effectiveShopIds;
                $shopBuckets[$bucketKey]['values'][$columnName] = $value;
            } else {
                $entityTableName ??= $definition->getExtraTableName();
                $entityValues[$columnName] = $value;
            }
        }

        if (!empty($entityValues) && null !== $entityTableName) {
            $this->writeCommon($entityTableName, $primaryKeyName, $entityId, $entityValues);
        }

        // LANG rows deliberately cover their FULL available scope, entity associations
        // ignored — native parity: ObjectModel::update() writes one {entity}_lang row per
        // context shop the same way.
        if (null !== $langTableName) {
            foreach ($langBuckets as $bucket) {
                if (empty($bucket['valuesByLang'])) {
                    continue;
                }
                $this->writeLang($langTableName, $primaryKeyName, $entityId, $bucket['shopIds'], $bucket['valuesByLang']);
            }
        }

        if (null !== $shopTableName) {
            foreach ($shopBuckets as $bucket) {
                // SHOP rows follow native {entity}_shop semantics: broad scopes (group, all
                // shops) only refresh the shops the entity is associated with, while an
                // explicitly named shop (single-shop constraint, ShopCollection) always gets
                // its row, like native CONTEXT_SHOP / $id_shop_list inserts.
                $shopScopeIds = $this->filterShopScopeByAssociations($entityName, $primaryKeyName, $entityId, $shopConstraint, $bucket['shopIds']);
                if (!empty($shopScopeIds)) {
                    $this->writeShop($shopTableName, $primaryKeyName, $entityId, $shopScopeIds, $bucket['values']);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toggleExtraProperty(
        ExtraPropertyDefinition $definition,
        int $entityId,
        ShopConstraint $shopConstraint,
        ?int $langId = null,
    ): void {
        if (ExtraPropertyType::BOOL !== $definition->getType()) {
            throw new InvalidArgumentException(sprintf(
                'Extra property "%s" is not of type BOOL and cannot be toggled.',
                $definition->getPropertyName()
            ));
        }

        $scope = $definition->getScope();
        if (ExtraPropertyScope::LANG === $scope && (null === $langId || $langId <= 0)) {
            throw new InvalidArgumentException(sprintf(
                'Toggling the LANG-scoped extra property "%s" requires a language id.',
                $definition->getPropertyName()
            ));
        }

        $isMultiShop = $definition->isMultiShop();
        $fullTableName = $this->prefix . $definition->getExtraTableName();
        $primaryKeyName = $definition->getPrimaryKeyName();
        $columnName = $definition->getStorageColumnName();

        $shopIds = [];
        $readShopId = null;
        if ($isMultiShop) {
            $shopIds = $this->shopListResolver->resolveShopIds($shopConstraint);
            // Definition availability first (a toggle must not create rows on shops the
            // definition does not exist for), then the entity association rule.
            $shopIds = $this->definitionShopFilter->getAvailableShopIds($definition, $shopIds);
            if (ExtraPropertyScope::SHOP === $scope) {
                // Same association rule as writeAll(): broad scopes only touch associated shops.
                $shopIds = $this->filterShopScopeByAssociations($definition->getEntityName(), $primaryKeyName, $entityId, $shopConstraint, $shopIds);
            }
            if ([] === $shopIds) {
                return;
            }
            // The representative shop's current value decides the target for the whole
            // scope, so a toggle in a group / all-shops context uniformizes shops that
            // diverged. When the association filter removed the representative, the lowest
            // remaining shop anchors the read instead.
            $readShopId = $this->shopListResolver->resolveRepresentativeShopId($shopConstraint);
            if (!in_array($readShopId, $shopIds, true)) {
                $readShopId = $shopIds[0];
            }
        } else {
            // Shared storage row: still a no-op when the definition is not available
            // anywhere in the scope (mirrors writeAll's collection filtering).
            $scopeShopIds = $this->shopListResolver->resolveShopIds($shopConstraint);
            if ([] !== $scopeShopIds && [] === $this->definitionShopFilter->getAvailableShopIds($definition, $scopeShopIds)) {
                return;
            }
        }

        // A missing row or a NULL value toggles to enabled, like the previous
        // "1 - IFNULL(col, 0)" upsert did.
        $keyColumns = [];
        if (null !== $readShopId) {
            $keyColumns['id_shop'] = $readShopId;
        }
        if (ExtraPropertyScope::LANG === $scope) {
            $keyColumns['id_lang'] = $langId;
        }
        $targetValue = $this->fetchCurrentBoolValue($fullTableName, $primaryKeyName, $entityId, $keyColumns, $columnName) ? 0 : 1;

        $rows = [];
        foreach ($isMultiShop ? $shopIds : [null] as $shopId) {
            $row = [$entityId];
            if (null !== $shopId) {
                $row[] = $shopId;
            }
            if (ExtraPropertyScope::LANG === $scope) {
                $row[] = $langId;
            }
            $row[] = $targetValue;
            $rows[] = $row;
        }

        $sql = $this->buildUpsertSql($fullTableName, $primaryKeyName, array_keys($keyColumns), [$columnName], count($rows));
        $this->connection->executeStatement($sql, array_merge(...$rows));
    }

    /**
     * Reads the current boolean value of one storage row. A missing row or a NULL value
     * reads as false (the toggle target is then "enabled").
     *
     * @param array<string, int> $keyColumns Additional key columns pinning the row (id_shop / id_lang)
     */
    protected function fetchCurrentBoolValue(string $fullTableName, string $primaryKeyName, int $entityId, array $keyColumns, string $columnName): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select($this->connection->quoteIdentifier($columnName))
            ->from($this->connection->quoteIdentifier($fullTableName))
            ->andWhere($this->connection->quoteIdentifier($primaryKeyName) . ' = :entityId')
            ->setParameter('entityId', $entityId);
        foreach ($keyColumns as $keyColumn => $keyValue) {
            $qb->andWhere($this->connection->quoteIdentifier($keyColumn) . ' = :' . $keyColumn)
                ->setParameter($keyColumn, $keyValue);
        }

        return (bool) $this->connection->fetchOne($qb->getSQL(), $qb->getParameters());
    }

    /**
     * Native-parity association filter for SHOP-scope rows: a broad constraint (shop group,
     * all shops) only refreshes the shops the entity is associated with in {entity}_shop —
     * like ObjectModel::update(), which never creates {entity}_shop rows in those contexts —
     * while explicitly named shops (single-shop constraint, ShopCollection) are always
     * written, like native CONTEXT_SHOP / $id_shop_list inserts. Entities without a
     * {entity}_shop association table keep the full scope.
     *
     * @param int[] $shopIds The constraint's resolved scope
     *
     * @return int[]
     */
    protected function filterShopScopeByAssociations(string $entityName, string $primaryKeyName, int $entityId, ShopConstraint $shopConstraint, array $shopIds): array
    {
        if (null !== $shopConstraint->getShopId()
            || ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds())
        ) {
            return $shopIds;
        }

        $qb = $this->connection->createQueryBuilder()
            ->select('a.id_shop')
            ->from($this->prefix . $entityName . '_shop', 'a')
            ->andWhere('a.' . $this->connection->quoteIdentifier($primaryKeyName) . ' = :entityId')
            ->setParameter('entityId', $entityId);

        try {
            $associatedShopIds = array_map(
                static fn (array $row): int => (int) $row['id_shop'],
                $this->connection->fetchAllAssociative($qb->getSQL(), $qb->getParameters())
            );
        } catch (Throwable) {
            // No {entity}_shop association table for this entity: the full scope applies.
            return $shopIds;
        }

        return array_values(array_intersect($shopIds, $associatedShopIds));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll(string $entityName, string $primaryKeyName, int $entityId): void
    {
        if ($entityId <= 0) {
            return;
        }

        $quotedPk = $this->connection->quoteIdentifier($primaryKeyName);

        foreach (ExtraPropertyScope::cases() as $scope) {
            $fullTable = $this->connection->quoteIdentifier(
                $this->prefix . ExtraPropertyDefinition::buildExtraTableName($entityName, $scope)
            );

            try {
                $this->connection->executeStatement(
                    sprintf('DELETE FROM %s WHERE %s = ?', $fullTable, $quotedPk),
                    [$entityId]
                );
            } catch (Throwable) {
                // Table may not exist if no extra properties have been registered — safe to ignore
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteForShops(string $entityName, string $primaryKeyName, int $entityId, array $shopIds): void
    {
        if ($entityId <= 0) {
            return;
        }
        $shopIds = array_values(array_filter(
            array_map('intval', $shopIds),
            static fn (int $shopId): bool => $shopId > 0
        ));
        if (empty($shopIds)) {
            return;
        }

        $definitions = $this->definitionRepository->getAllDefinitions()->filterByEntity($entityName);
        if ($definitions->isEmpty()) {
            return;
        }

        $quotedPk = $this->connection->quoteIdentifier($primaryKeyName);
        foreach ([ExtraPropertyScope::SHOP, ExtraPropertyScope::LANG] as $scope) {
            $scoped = $definitions->filterByScope($scope);
            // Only tables that key rows per shop hold shop-specific rows to remove; a
            // non-multishop lang table is shared by every shop and must survive.
            if ($scoped->isEmpty() || !$scoped->first()->isMultiShop()) {
                continue;
            }

            $fullTable = $this->connection->quoteIdentifier($this->prefix . $scoped->first()->getExtraTableName());

            try {
                $this->connection->executeStatement(
                    sprintf('DELETE FROM %s WHERE %s = ? AND id_shop IN (?)', $fullTable, $quotedPk),
                    [$entityId, $shopIds],
                    [1 => ArrayParameterType::INTEGER]
                );
            } catch (Throwable) {
                // Table may not exist if no extra properties have been registered — safe to ignore
            }
        }
    }

    /**
     * Writes common-scope (entity-level) values for one entity instance.
     *
     * @param string $extraTableName Extra table name without DB prefix (from ExtraPropertyDefinition::getExtraTableName())
     * @param array<string, mixed> $columnValues
     */
    protected function writeCommon(string $extraTableName, string $primaryKeyName, int $entityId, array $columnValues): void
    {
        $sql = $this->buildUpsertSql($this->prefix . $extraTableName, $primaryKeyName, [], array_keys($columnValues));
        $this->connection->executeStatement($sql, [$entityId, ...array_values($columnValues)]);
    }

    /**
     * Writes lang-scope values for one entity instance: one row per language, times one
     * per shop when the lang table is shop-aware — batched into a single multi-row UPSERT
     * per distinct column set (an all-shops save is one statement, not shops × languages
     * round trips). Languages carrying different column sets (a value provided for some
     * languages only) get their own statement so absent columns are never overwritten.
     *
     * @param string $extraTableName Extra table name without DB prefix (from ExtraPropertyDefinition::getExtraTableName())
     * @param int[]|null $shopIds Shops the rows belong to; null when the entity's lang table has no id_shop column
     * @param array<int, array<string, mixed>> $langValuesByIdLang [idLang => ['column' => value]]
     */
    protected function writeLang(string $extraTableName, string $primaryKeyName, int $entityId, ?array $shopIds, array $langValuesByIdLang): void
    {
        if (null !== $shopIds && [] === $shopIds) {
            return;
        }

        $fullTableName = $this->prefix . $extraTableName;
        $systemColumns = null !== $shopIds ? ['id_shop', 'id_lang'] : ['id_lang'];

        // Group languages sharing the same column set into one multi-row statement.
        $langIdsByColumnSet = [];
        foreach ($langValuesByIdLang as $idLang => $columnValues) {
            if (empty($columnValues)) {
                continue;
            }
            $langIdsByColumnSet[implode(',', array_keys($columnValues))][] = (int) $idLang;
        }

        foreach ($langIdsByColumnSet as $langIds) {
            $dataColumns = array_keys($langValuesByIdLang[$langIds[0]]);
            $rows = [];
            foreach ($shopIds ?? [null] as $shopId) {
                foreach ($langIds as $idLang) {
                    $row = [$entityId];
                    if (null !== $shopId) {
                        $row[] = $shopId;
                    }
                    $row[] = $idLang;
                    array_push($row, ...array_values($langValuesByIdLang[$idLang]));
                    $rows[] = $row;
                }
            }

            $sql = $this->buildUpsertSql($fullTableName, $primaryKeyName, $systemColumns, $dataColumns, count($rows));
            $this->connection->executeStatement($sql, array_merge(...$rows));
        }
    }

    /**
     * Writes shop-scope values for one entity instance: one row per shop, batched into a
     * single multi-row UPSERT.
     *
     * @param string $extraTableName Extra table name without DB prefix (from ExtraPropertyDefinition::getExtraTableName())
     * @param int[] $shopIds
     * @param array<string, mixed> $columnValues
     */
    protected function writeShop(string $extraTableName, string $primaryKeyName, int $entityId, array $shopIds, array $columnValues): void
    {
        $rows = [];
        foreach ($shopIds as $shopId) {
            $rows[] = [$entityId, $shopId, ...array_values($columnValues)];
        }

        $sql = $this->buildUpsertSql($this->prefix . $extraTableName, $primaryKeyName, ['id_shop'], array_keys($columnValues), count($rows));
        $this->connection->executeStatement($sql, array_merge(...$rows));
    }

    /**
     * Builds a (multi-row) INSERT … ON DUPLICATE KEY UPDATE statement.
     *
     * $systemColumns are fixed keys inserted before the data columns (e.g. id_shop, id_lang).
     * Callers must pass parameters row by row, each row in order: entityId, systemColumn
     * values, then data values.
     *
     * @param string[] $systemColumns Fixed system key column names (order matters for bindings)
     * @param string[] $dataColumns Data column names (order matters for bindings)
     * @param int $rowCount Number of value rows the statement covers
     */
    protected function buildUpsertSql(string $fullTableName, string $primaryKeyName, array $systemColumns, array $dataColumns, int $rowCount = 1): string
    {
        $quotedPk = $this->connection->quoteIdentifier($primaryKeyName);
        $quotedSystemCols = array_map(
            fn (string $col): string => $this->connection->quoteIdentifier($col),
            $systemColumns
        );
        $quotedDataCols = array_map(
            fn (string $col): string => $this->connection->quoteIdentifier($col),
            $dataColumns
        );

        $allColsList = implode(', ', [$quotedPk, ...($quotedSystemCols ?: []), ...$quotedDataCols]);
        $rowPlaceholders = '(' . implode(', ', array_fill(0, 1 + count($systemColumns) + count($dataColumns), '?')) . ')';
        $updateParts = implode(', ', array_map(
            fn (string $quotedCol): string => $quotedCol . ' = VALUES(' . $quotedCol . ')',
            $quotedDataCols
        ));

        return sprintf(
            'INSERT INTO %s (%s) VALUES %s ON DUPLICATE KEY UPDATE %s',
            $this->connection->quoteIdentifier($fullTableName),
            $allColsList,
            implode(', ', array_fill(0, $rowCount, $rowPlaceholders)),
            $updateParts
        );
    }
}
