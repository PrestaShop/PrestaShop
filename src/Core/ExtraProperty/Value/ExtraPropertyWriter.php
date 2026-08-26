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
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
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
 */
class ExtraPropertyWriter implements ExtraPropertyWriterInterface
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $prefix,
        protected readonly ExtraPropertyDefinitionRepositoryInterface $definitionRepository,
        protected readonly ShopListResolverInterface $shopListResolver,
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

        $entityValues = [];
        $langValuesByIdLang = [];
        $shopValues = [];
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
                if (is_array($value)) {
                    // Multilang array: one entry per language.
                    foreach ($value as $langId => $langValue) {
                        if ((int) $langId <= 0 || (null === $langValue && !$isNullable)) {
                            continue;
                        }
                        $langValuesByIdLang[(int) $langId][$columnName] = $langValue;
                    }
                } elseif (null !== $defaultLangId && $defaultLangId > 0) {
                    // Scalar lang value: written for the caller-provided language only.
                    $langValuesByIdLang[$defaultLangId][$columnName] = $value;
                }
            } elseif (ExtraPropertyScope::SHOP === $definition->getScope()) {
                $shopTableName ??= $definition->getExtraTableName();
                $shopValues[$columnName] = $value;
            } else {
                $entityTableName ??= $definition->getExtraTableName();
                $entityValues[$columnName] = $value;
            }
        }

        // One row per shop covered by the constraint — native ObjectModel parity
        // (a group / all-shops edit updates every shop in scope).
        $shopIds = $this->shopListResolver->resolveShopIds($shopConstraint);

        if (!empty($entityValues) && null !== $entityTableName) {
            $this->writeCommon($entityTableName, $primaryKeyName, $entityId, $entityValues);
        }

        if (!empty($langValuesByIdLang) && null !== $langTableName) {
            if ($langIsMultiShop) {
                foreach ($shopIds as $shopId) {
                    $this->writeLang($langTableName, $primaryKeyName, $entityId, $shopId, $langValuesByIdLang);
                }
            } else {
                // The entity's lang table has no id_shop column: one row per language, shared by all shops.
                $this->writeLang($langTableName, $primaryKeyName, $entityId, null, $langValuesByIdLang);
            }
        }

        if (!empty($shopValues) && null !== $shopTableName) {
            foreach ($shopIds as $shopId) {
                $this->writeShop($shopTableName, $primaryKeyName, $entityId, $shopId, $shopValues);
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

        // The representative shop's current value decides the target for the whole scope,
        // so a toggle in a group / all-shops context uniformizes shops that diverged.
        // A missing row or a NULL value toggles to enabled, like the previous
        // "1 - IFNULL(col, 0)" upsert did.
        $keyColumns = [];
        if ($isMultiShop) {
            $keyColumns['id_shop'] = $this->shopListResolver->resolveRepresentativeShopId($shopConstraint);
        }
        if (ExtraPropertyScope::LANG === $scope) {
            $keyColumns['id_lang'] = $langId;
        }
        $targetValue = $this->fetchCurrentBoolValue($fullTableName, $primaryKeyName, $entityId, $keyColumns, $columnName) ? 0 : 1;

        $sql = $this->buildUpsertSql($fullTableName, $primaryKeyName, array_keys($keyColumns), [$columnName => $targetValue]);
        foreach ($isMultiShop ? $this->shopListResolver->resolveShopIds($shopConstraint) : [null] as $shopId) {
            $params = [$entityId];
            if (null !== $shopId) {
                $params[] = $shopId;
            }
            if (ExtraPropertyScope::LANG === $scope) {
                $params[] = $langId;
            }
            $params[] = $targetValue;
            $this->connection->executeStatement($sql, $params);
        }
    }

    /**
     * Reads the current boolean value of one storage row. A missing row or a NULL value
     * reads as false (the toggle target is then "enabled").
     *
     * @param array<string, int> $keyColumns Additional key columns pinning the row (id_shop / id_lang)
     */
    protected function fetchCurrentBoolValue(string $fullTableName, string $primaryKeyName, int $entityId, array $keyColumns, string $columnName): bool
    {
        $conditions = [$this->connection->quoteIdentifier($primaryKeyName) . ' = ?'];
        $params = [$entityId];
        foreach ($keyColumns as $keyColumn => $keyValue) {
            $conditions[] = $this->connection->quoteIdentifier($keyColumn) . ' = ?';
            $params[] = $keyValue;
        }

        $sql = sprintf(
            'SELECT %s FROM %s WHERE %s',
            $this->connection->quoteIdentifier($columnName),
            $this->connection->quoteIdentifier($fullTableName),
            implode(' AND ', $conditions)
        );

        return (bool) $this->connection->fetchOne($sql, $params);
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
        $sql = $this->buildUpsertSql($this->prefix . $extraTableName, $primaryKeyName, [], $columnValues);
        $this->connection->executeStatement($sql, [$entityId, ...array_values($columnValues)]);
    }

    /**
     * Writes lang-scope values for one entity instance, one row per language.
     *
     * @param string $extraTableName Extra table name without DB prefix (from ExtraPropertyDefinition::getExtraTableName())
     * @param int|null $shopId Shop the rows belong to; null when the entity's lang table has no id_shop column
     * @param array<int, array<string, mixed>> $langValuesByIdLang [idLang => ['column' => value]]
     */
    protected function writeLang(string $extraTableName, string $primaryKeyName, int $entityId, ?int $shopId, array $langValuesByIdLang): void
    {
        $fullTableName = $this->prefix . $extraTableName;
        $systemColumns = null !== $shopId ? ['id_shop', 'id_lang'] : ['id_lang'];

        foreach ($langValuesByIdLang as $idLang => $columnValues) {
            if (empty($columnValues)) {
                continue;
            }
            $sql = $this->buildUpsertSql($fullTableName, $primaryKeyName, $systemColumns, $columnValues);
            $params = null !== $shopId
                ? [$entityId, $shopId, (int) $idLang, ...array_values($columnValues)]
                : [$entityId, (int) $idLang, ...array_values($columnValues)];
            $this->connection->executeStatement($sql, $params);
        }
    }

    /**
     * Writes shop-scope values for one entity instance.
     *
     * @param string $extraTableName Extra table name without DB prefix (from ExtraPropertyDefinition::getExtraTableName())
     * @param array<string, mixed> $columnValues
     */
    protected function writeShop(string $extraTableName, string $primaryKeyName, int $entityId, int $shopId, array $columnValues): void
    {
        $sql = $this->buildUpsertSql($this->prefix . $extraTableName, $primaryKeyName, ['id_shop'], $columnValues);
        $this->connection->executeStatement($sql, [$entityId, $shopId, ...array_values($columnValues)]);
    }

    /**
     * Builds an INSERT … ON DUPLICATE KEY UPDATE statement.
     *
     * $systemColumns are fixed keys inserted before the data columns (e.g. id_shop, id_lang).
     * Callers must pass parameters in order: entityId, systemColumn values, then data values.
     *
     * @param string[] $systemColumns Fixed system key column names (order matters for bindings)
     * @param array<string, mixed> $columnValues Data column name → value map
     */
    protected function buildUpsertSql(string $fullTableName, string $primaryKeyName, array $systemColumns, array $columnValues): string
    {
        $quotedPk = $this->connection->quoteIdentifier($primaryKeyName);
        $quotedSystemCols = array_map(
            fn (string $col): string => $this->connection->quoteIdentifier($col),
            $systemColumns
        );
        $quotedDataCols = array_map(
            fn (string $col): string => $this->connection->quoteIdentifier($col),
            array_keys($columnValues)
        );

        $allColsList = implode(', ', [$quotedPk, ...($quotedSystemCols ?: []), ...$quotedDataCols]);
        $allPlaceholders = implode(', ', array_fill(0, 1 + count($systemColumns) + count($columnValues), '?'));
        $updateParts = implode(', ', array_map(
            fn (string $quotedCol): string => $quotedCol . ' = VALUES(' . $quotedCol . ')',
            $quotedDataCols
        ));

        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
            $this->connection->quoteIdentifier($fullTableName),
            $allColsList,
            $allPlaceholders,
            $updateParts
        );
    }
}
