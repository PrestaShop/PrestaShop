<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDefinitionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ProtectedModuleExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ColumnDefinitionMapper;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyValueCaster;
use Throwable;

/**
 * Reads and writes extra property definitions in the extra_property_definition registry table.
 *
 * This implementation does not add any caching; wrap with Definition\CachedExtraPropertyDefinitionRepository
 * for production use.
 *
 * All public read methods return typed ExtraPropertyDefinition value objects or collections.
 */
class ExtraPropertyDefinitionRepository implements ExtraPropertyDefinitionRepositoryInterface, ExtraPropertyDefinitionWriterInterface
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $prefix,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDefinitions(): ExtraPropertyDefinitionCollection
    {
        $table = $this->prefix . 'extra_property_definition';
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('eef.*')
            ->from($table, 'eef')
            ->orderBy('eef.id_extra_property_definition', 'ASC');

        $rows = $this->enrichRowsWithShopAssociations(
            $this->enrichRowsWithColumnMetadata($qb->executeQuery()->fetchAllAssociative() ?: [])
        );

        return new ExtraPropertyDefinitionCollection(array_values(array_map(
            static fn (array $row): ExtraPropertyDefinition => ExtraPropertyDefinition::fromRow($row),
            $rows
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function findDefinitionByModuleAndField(string $entityName, ?string $moduleName, string $fieldName): ?ExtraPropertyDefinition
    {
        $table = $this->prefix . 'extra_property_definition';
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('eef.*')
            ->from($table, 'eef')
            ->where('eef.entity_name = :entityName')
            ->andWhere('eef.property_name = :fieldName')
            ->setParameter('entityName', $entityName)
            ->setParameter('fieldName', $fieldName);

        $this->applyModuleNameFilter($qb, $moduleName, 'eef');

        $row = $qb->executeQuery()->fetchAssociative();
        if (!is_array($row)) {
            return null;
        }

        return ExtraPropertyDefinition::fromRow($this->enrichRowsWithShopAssociations($this->enrichRowsWithColumnMetadata([$row]))[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionById(int $id): ?ExtraPropertyDefinition
    {
        $table = $this->prefix . 'extra_property_definition';
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('eef.*')
            ->from($table, 'eef')
            ->where('eef.id_extra_property_definition = :id')
            ->setParameter('id', $id);

        $row = $qb->executeQuery()->fetchAssociative();
        if (!is_array($row)) {
            return null;
        }

        return ExtraPropertyDefinition::fromRow($this->enrichRowsWithShopAssociations($this->enrichRowsWithColumnMetadata([$row]))[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function getUnprotectedDefinitionById(int $id): ExtraPropertyDefinition
    {
        $definition = $this->getDefinitionById($id);

        if (null === $definition) {
            throw new ExtraPropertyDefinitionNotFoundException(
                sprintf('Extra property definition with id %d was not found.', $id)
            );
        }

        if ($definition->isModuleOwned()) {
            throw new ProtectedModuleExtraPropertyDefinitionException(
                sprintf(
                    'Extra property definition "%s.%s" is owned by module "%s" and cannot be modified from the BO.',
                    $definition->getEntityName(),
                    $definition->getPropertyName(),
                    $definition->getModuleName()
                )
            );
        }

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ExtraPropertyDefinition $definition): int|false
    {
        $table = $this->prefix . 'extra_property_definition';

        $data = [
            // getModuleName() is already normalized: null for core fields ('' / '_core' inputs included).
            'module_name' => $definition->getModuleName(),
            // Stored resolved: fromRow() passes it back as the explicit value, so hydration never re-resolves.
            'table_name' => $definition->getTableName(),
            // Stored as override only (null for deduced values): the permission subject must
            // follow core code as BO tabs evolve, unlike the frozen storage location above.
            'controller_name' => $definition->getControllerNameOverride(),
            'scope' => $definition->getScope()->value,
            'type' => $definition->getType()->value,
            'size' => $definition->getSize(),
            'required' => (int) $definition->isRequired(),
            // Shared canonical stringification (BOOL → '1'/'0'): a naive (string) cast would
            // turn false into '', which fromRow() reads back as "no default".
            'default_value' => ExtraPropertyValueCaster::castDefaultValueForDb($definition->getType(), $definition->getDefaultValue()),
            'form_type' => $definition->getFormType(),
            'form_options' => null !== $definition->getFormOptions() ? json_encode($definition->getFormOptions()) : null,
            'sql_index' => $definition->getSqlIndex()->value,
            'constraints' => !empty($definition->getConstraints()) ? serialize($definition->getConstraints()) : null,
            'associated_forms' => !empty($definition->getAssociatedForms()) ? json_encode(array_values($definition->getAssociatedForms())) : null,
            'associated_grids' => !empty($definition->getAssociatedGrids()) ? json_encode(array_values($definition->getAssociatedGrids())) : null,
            'associated_apis' => !empty($definition->getAssociatedApis()) ? json_encode(array_values($definition->getAssociatedApis())) : null,
            'display_front' => (int) $definition->isDisplayFront(),
            'label_wording' => $definition->getLabelWording(),
            'label_domain' => $definition->getLabelDomain(),
            'description_wording' => $definition->getDescriptionWording(),
            'description_domain' => $definition->getDescriptionDomain(),
        ];

        // Resolve existing row ID from the unique key to decide INSERT vs UPDATE.
        $existingId = $this->findIdByUniqueKey(
            $definition->getEntityName(),
            $definition->getModuleName(),
            $definition->getPropertyName()
        );

        if (null !== $existingId) {
            // Doctrine's update() returns the affected row count, not a "found" count: it is
            // legitimately 0 when none of the *registry* columns actually changed (e.g. editing
            // only nullable/enumValues, which are never persisted here — see fromRow()/the class
            // docblock). $existingId is already known to exist (just resolved above), so the
            // update is considered successful as long as it does not throw.
            $this->connection->update($table, $data, ['id_extra_property_definition' => $existingId]);
            $this->persistShopAssociation($existingId, $definition);

            return $existingId;
        }

        $data['entity_name'] = $definition->getEntityName();
        $data['property_name'] = $definition->getPropertyName();

        $saved = (bool) $this->connection->insert($table, $data);
        if (!$saved) {
            return false;
        }

        $definitionId = (int) $this->connection->lastInsertId();
        $this->persistShopAssociation($definitionId, $definition);

        return $definitionId;
    }

    /**
     * Persists the definition's shop association as part of save() — the definition is
     * the single write path for the association — honoring the associatedShopIds
     * tri-state (see the ExtraPropertyDefinition property docblock): null = no
     * information, the stored association is left untouched — so a module re-registering
     * its definition without shop data cannot clobber a BO-configured restriction;
     * [] or a list = the stored extra_property_definition_shop rows are replaced
     * ([] deletes them all, reverting to the fallback behavior).
     *
     * The ids are written as-is (no FK on the table): their existence is validated
     * upstream by ExtraPropertyRegistry::register(), the single definition write
     * choke point, before any DDL or row write.
     */
    protected function persistShopAssociation(int $definitionId, ExtraPropertyDefinition $definition): void
    {
        // Already normalized by the ExtraPropertyDefinition constructor (int cast, deduplicated).
        $shopIds = $definition->getAssociatedShopIds();
        if (null === $shopIds) {
            return;
        }

        $table = $this->prefix . 'extra_property_definition_shop';

        $this->connection->transactional(function () use ($table, $definitionId, $shopIds): void {
            $this->connection->delete($table, ['id_extra_property_definition' => $definitionId]);
            foreach ($shopIds as $shopId) {
                $this->connection->insert($table, [
                    'id_extra_property_definition' => $definitionId,
                    'id_shop' => $shopId,
                ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $table = $this->prefix . 'extra_property_definition';

        $deleted = (bool) $this->connection->delete($table, ['id_extra_property_definition' => $id]);
        if ($deleted) {
            // The core schema has no FK constraints: purge the shop association rows explicitly.
            // Done after the registry row so a failure here leaves harmless unreferenced rows
            // instead of a definition without its restriction (same ordering rationale as
            // ExtraPropertyRegistry::unregister()).
            $this->connection->delete($this->prefix . 'extra_property_definition_shop', ['id_extra_property_definition' => $id]);
        }

        return $deleted;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByDefinition(ExtraPropertyDefinition $definition): bool
    {
        $id = $this->findIdByUniqueKey(
            $definition->getEntityName(),
            $definition->getModuleName(),
            $definition->getPropertyName()
        );
        if (null === $id) {
            return true;
        }

        return $this->delete($id);
    }

    /**
     * Looks up the primary key for a definition identified by its unique key
     * (entity + module + property — unique across scopes).
     *
     * Returns null when no matching row exists.
     */
    protected function findIdByUniqueKey(string $entityName, ?string $moduleName, string $propertyName): ?int
    {
        $table = $this->prefix . 'extra_property_definition';
        $qb = $this->connection->createQueryBuilder();
        $qb->select('id_extra_property_definition')
            ->from($table)
            ->where('entity_name = :entityName')
            ->andWhere('property_name = :propertyName')
            ->setParameter('entityName', $entityName)
            ->setParameter('propertyName', $propertyName);

        $this->applyModuleNameFilter($qb, $moduleName);

        $id = $qb->executeQuery()->fetchOne();

        return false !== $id && null !== $id ? (int) $id : null;
    }

    /**
     * Applies a WHERE clause for module_name on a query builder.
     *
     * Uses `module_name IS NULL` for core fields (null/empty) since SQL `= NULL` never matches.
     *
     * @param QueryBuilder $qb Query builder to modify in place
     * @param string|null $moduleName Module name, or null/'' for core fields
     * @param string $alias Optional table alias prefix (e.g. 'eef' → 'eef.module_name')
     */
    protected function applyModuleNameFilter(QueryBuilder $qb, ?string $moduleName, string $alias = ''): void
    {
        $column = ('' !== $alias) ? $alias . '.module_name' : 'module_name';

        if (null !== $moduleName && '' !== $moduleName) {
            $qb->andWhere($column . ' = :moduleName')->setParameter('moduleName', $moduleName);
        } else {
            $qb->andWhere($column . ' IS NULL');
        }
    }

    /**
     * Enriches registry rows with the synthetic 'nullable', 'enum_values' and 'multi_shop'
     * keys, deduced from the live DB structure of each definition's storage table/column.
     * These attributes are not persisted in the registry table: the extra table schema is
     * their source of truth (NULL/NOT NULL clause, ENUM literals for CHOICE columns,
     * presence of an id_shop column for per-shop storage).
     *
     * One SHOW COLUMNS query per distinct extra table; getAllDefinitions() results are cached
     * by CachedExtraPropertyDefinitionRepository, so the introspection cost is amortized.
     * Rows whose storage column does not exist (yet) are left untouched — fromRow() then
     * applies its safe defaults (nullable, no enum).
     *
     * @param array<int, array<string, mixed>> $rows
     *
     * @return array<int, array<string, mixed>>
     */
    protected function enrichRowsWithColumnMetadata(array $rows): array
    {
        $columnsByTable = [];

        foreach ($rows as &$row) {
            $scope = ExtraPropertyScope::tryFrom((string) ($row['scope'] ?? '')) ?? ExtraPropertyScope::COMMON;
            // The stored physical table (table_name), never the logical entity name — rows
            // predating the column fall back to entity_name, correct for conventional naming.
            $entityTable = isset($row['table_name']) && '' !== $row['table_name']
                ? (string) $row['table_name']
                : (string) ($row['entity_name'] ?? '');
            $tableName = $this->prefix . ExtraPropertyDefinition::buildExtraTableName($entityTable, $scope);
            $columnName = ExtraPropertyDefinition::buildStorageColumnName(
                isset($row['module_name']) && '' !== $row['module_name'] ? (string) $row['module_name'] : null,
                (string) ($row['property_name'] ?? '')
            );

            if (!array_key_exists($tableName, $columnsByTable)) {
                $columnsByTable[$tableName] = $this->fetchColumnMetadata($tableName);
            }

            // Per-shop storage is a property of the table, not of the definition's own column:
            // inject it whenever the table exists, even if the storage column is missing.
            if ([] !== $columnsByTable[$tableName]) {
                $row['multi_shop'] = array_key_exists('id_shop', $columnsByTable[$tableName]);

                // The live extra-table PK is the source of truth for the entity id column
                // (the schema manager mirrored it from the base table at creation): among
                // the PRI columns, the single one that is not the lang/shop dimension.
                // Ambiguity (composite base PKs) injects nothing — the VO then falls back
                // to its own resolution (ObjectModel class, then naming convention).
                $primaryColumns = array_keys(array_filter(
                    $columnsByTable[$tableName],
                    static fn (array $columnMetadata, string $column): bool => $columnMetadata['primary'] && !in_array($column, ['id_lang', 'id_shop'], true),
                    ARRAY_FILTER_USE_BOTH
                ));
                if (1 === count($primaryColumns)) {
                    $row['primary_key_name'] = $primaryColumns[0];
                }
            }

            $columnMetadata = $columnsByTable[$tableName][$columnName] ?? null;
            if (null === $columnMetadata) {
                continue;
            }

            $row['nullable'] = $columnMetadata['nullable'];
            $row['enum_values'] = $columnMetadata['enum_values'];
        }

        return $rows;
    }

    /**
     * Enriches registry rows with the synthetic 'associated_shop_ids' key, loaded from the
     * extra_property_definition_shop association table. Like 'multi_shop', it is not a registry
     * column — fromRow() consumes it to expose ExtraPropertyDefinition::getAssociatedShopIds().
     * Rows without association rows are left untouched (null = no explicit restriction, see
     * ExtraPropertyDefinition::isAvailableForShops()).
     *
     * One query for the whole batch, keyed on the rows' primary keys.
     *
     * @param array<int, array<string, mixed>> $rows
     *
     * @return array<int, array<string, mixed>>
     */
    protected function enrichRowsWithShopAssociations(array $rows): array
    {
        $definitionIds = array_values(array_filter(array_map(
            static fn (array $row): int => (int) ($row['id_extra_property_definition'] ?? 0),
            $rows
        )));
        if ([] === $definitionIds) {
            return $rows;
        }

        $associations = $this->connection->createQueryBuilder()
            ->select('eps.id_extra_property_definition, eps.id_shop')
            ->from($this->prefix . 'extra_property_definition_shop', 'eps')
            ->where('eps.id_extra_property_definition IN (:definitionIds)')
            ->setParameter('definitionIds', $definitionIds, Connection::PARAM_INT_ARRAY)
            ->executeQuery()
            ->fetchAllAssociative();

        $shopIdsByDefinition = [];
        foreach ($associations as $association) {
            $shopIdsByDefinition[(int) $association['id_extra_property_definition']][] = (int) $association['id_shop'];
        }

        foreach ($rows as &$row) {
            $definitionId = (int) ($row['id_extra_property_definition'] ?? 0);
            if (isset($shopIdsByDefinition[$definitionId])) {
                $row['associated_shop_ids'] = $shopIdsByDefinition[$definitionId];
            }
        }

        return $rows;
    }

    /**
     * Introspects an extra table and returns nullability + ENUM literals per column.
     *
     * Returns an empty array when the table does not exist (no extra property value was
     * ever registered for that entity/scope combination yet).
     *
     * @param string $tableName Full table name (with prefix)
     *
     * @return array<string, array{nullable: bool, enum_values: list<string>|null, primary: bool}> keyed by column name
     */
    protected function fetchColumnMetadata(string $tableName): array
    {
        try {
            $columns = $this->connection->fetchAllAssociative(
                'SHOW COLUMNS FROM ' . $this->connection->quoteIdentifier($tableName)
            );
        } catch (Throwable) {
            return [];
        }

        $metadata = [];
        foreach ($columns as $column) {
            $metadata[(string) $column['Field']] = [
                'nullable' => 'YES' === strtoupper((string) ($column['Null'] ?? 'YES')),
                'enum_values' => ColumnDefinitionMapper::parseEnumValues((string) ($column['Type'] ?? '')),
                'primary' => 'PRI' === strtoupper((string) ($column['Key'] ?? '')),
            ];
        }

        return $metadata;
    }
}
