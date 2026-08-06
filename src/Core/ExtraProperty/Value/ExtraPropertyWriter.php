<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Value;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
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
 */
class ExtraPropertyWriter implements ExtraPropertyWriterInterface
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $prefix,
        protected readonly ExtraPropertyDefinitionRepositoryInterface $definitionRepository,
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

        $shopId = $shopConstraint->isSingleShopContext() ? $shopConstraint->getShopId()->getValue() : null;

        if (!empty($entityValues) && null !== $entityTableName) {
            $this->writeCommon($entityTableName, $primaryKeyName, $entityId, $entityValues);
        }

        if (!empty($langValuesByIdLang) && null !== $langTableName && null !== $shopId) {
            $this->writeLang($langTableName, $primaryKeyName, $entityId, $shopId, $langValuesByIdLang);
        }

        if (!empty($shopValues) && null !== $shopTableName && null !== $shopId) {
            $this->writeShop($shopTableName, $primaryKeyName, $entityId, $shopId, $shopValues);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toggleExtraProperty(
        ExtraPropertyDefinition $definition,
        int $entityId,
        ShopConstraint $shopConstraint,
    ): void {
        if (ExtraPropertyType::BOOL !== $definition->getType()) {
            throw new InvalidArgumentException(sprintf(
                'Extra property "%s" is not of type BOOL and cannot be toggled.',
                $definition->getPropertyName()
            ));
        }

        $fullTableName = $this->connection->quoteIdentifier($this->prefix . $definition->getExtraTableName());
        $quotedPk = $this->connection->quoteIdentifier($definition->getPrimaryKeyName());
        $quotedCol = $this->connection->quoteIdentifier($definition->getStorageColumnName());

        if (ExtraPropertyScope::SHOP === $definition->getScope()) {
            $shopId = $shopConstraint->isSingleShopContext() ? $shopConstraint->getShopId()->getValue() : null;
            if (null === $shopId) {
                throw new InvalidArgumentException(sprintf(
                    'Toggling the SHOP-scoped extra property "%s" requires a single-shop constraint.',
                    $definition->getPropertyName()
                ));
            }

            $sql = sprintf(
                'INSERT INTO %s (%s, %s, %s) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE %s = 1 - IFNULL(%s, 0)',
                $fullTableName,
                $quotedPk,
                $this->connection->quoteIdentifier('id_shop'),
                $quotedCol,
                $quotedCol,
                $quotedCol,
            );
            $this->connection->executeStatement($sql, [$entityId, $shopId]);
        } else {
            $sql = sprintf(
                'INSERT INTO %s (%s, %s) VALUES (?, 1) ON DUPLICATE KEY UPDATE %s = 1 - IFNULL(%s, 0)',
                $fullTableName,
                $quotedPk,
                $quotedCol,
                $quotedCol,
                $quotedCol,
            );
            $this->connection->executeStatement($sql, [$entityId]);
        }
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
     * @param array<int, array<string, mixed>> $langValuesByIdLang [idLang => ['column' => value]]
     */
    protected function writeLang(string $extraTableName, string $primaryKeyName, int $entityId, int $shopId, array $langValuesByIdLang): void
    {
        $fullTableName = $this->prefix . $extraTableName;

        foreach ($langValuesByIdLang as $idLang => $columnValues) {
            if (empty($columnValues)) {
                continue;
            }
            $sql = $this->buildUpsertSql($fullTableName, $primaryKeyName, ['id_shop', 'id_lang'], $columnValues);
            $this->connection->executeStatement($sql, [$entityId, $shopId, (int) $idLang, ...array_values($columnValues)]);
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
