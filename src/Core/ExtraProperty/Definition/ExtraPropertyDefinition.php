<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyValidator;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyValueCaster;
use PrestaShop\PrestaShop\Core\Util\Inflector;
use Symfony\Component\Validator\Constraint;

/**
 * Immutable value object representing an extra property definition.
 *
 * Serves as both the module-facing configuration object (passed to Module::registerExtraProperty())
 * and the internal read DTO (returned by repository methods). All fields use typed Enum values
 * instead of raw strings for type safety and PHPStan coverage.
 *
 * Fields used only at schema-creation time (not persisted in the registry):
 *   - $nullable: NULL vs NOT NULL in the DDL
 *   - $enumValues: ENUM literals for ExtraPropertyType::CHOICE fields
 *
 * Use the static factory ExtraPropertyDefinition::fromRow() to build an instance from a DB row.
 * Use withModuleName() to derive a copy with injected module context.
 *
 * Constructor validation:
 * - entityName and propertyName are required and must be non-empty.
 * - associatedForms: each entry must match "formId[:path[:before|after]]"; no duplicate formId.
 * - associatedGrids: each entry must match "gridId[:columnId[:before|after]]"; no duplicate gridId.
 * - associatedApis: each entry must match "uriPath[:METHOD[,METHOD...]]"; uriPath is the operation URI template.
 * - labelWording is required when associatedForms or associatedGrids is non-empty.
 *
 * @see \PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface::register()
 * @see \PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ColumnDefinitionMapper
 */
final class ExtraPropertyDefinition
{
    /**
     * Module-name key used in grouped arrays for fields that have no owning module (core fields).
     */
    public const CORE_MODULE_KEY = '_core';

    /**
     * Owning module technical name. Always null for core fields: '' and the '_core'
     * sentinel are normalized to null at construction, so getModuleName() needs no
     * defensive re-normalization by callers.
     */
    protected readonly ?string $moduleName;

    /**
     * Module key used in grouped extra-property arrays (reader output, writer input,
     * bags, form field names): the module technical name, or '_core' for core fields.
     * Computed once at construction — the exact counterpart of $moduleName.
     */
    protected readonly string $normalizedModuleKey;

    /**
     * Entity table name, normalized to lower snake_case at construction: it is a SQL
     * table name fragment ({entity}_extra tables) and the base of the primary key
     * column name (id_{entity}), so casing/hyphens never leak into identifiers.
     */
    protected readonly string $entityName;

    /**
     * @param string $entityName Entity table name (e.g. 'product'). Required — must be non-empty. Normalized to lower snake_case at construction.
     * @param string $propertyName Property name as declared by the module (e.g. 'video_link'). Required.
     * @param ExtraPropertyType $type Field storage type. Determines the SQL column type via ColumnDefinitionMapper.
     * @param ExtraPropertyScope $scope Storage scope: COMMON (entity-level), LANG (per-language), SHOP (per-shop)
     * @param string|null $moduleName Owning module name. Null = core field ('' and '_core' are normalized to null). Auto-populated by Module::registerExtraProperty().
     * @param list<string>|null $enumValues For CHOICE type: SQL ENUM allowed values. Not persisted — schema creation only.
     * @param scalar|null $defaultValue Adds a DEFAULT clause in DDL. Also persisted in registry.
     * @param bool $nullable Controls NULL vs NOT NULL in DDL. Not persisted — schema creation only.
     * @param bool $required when true, marks the field as required in BO forms and in the Admin API (OpenAPI) schema
     * @param int|null $size for STRING type: varchar column length (defaults to 255)
     * @param ExtraPropertySqlIndex $sqlIndex SQL index strategy on the storage column
     * @param bool $displayFront allow this field to be exposed in front-office presenters
     * @param list<string>|null $associatedForms Form placement entries: "formId[:path[:before|after]]". Each formId must be unique.
     * @param list<string>|null $associatedGrids Grid placement entries: "gridId[:columnId[:before|after]]". Each gridId must be unique.
     * @param list<string>|null $associatedApis Admin API placement entries: "uriPath[:METHOD[,METHOD...]]", matched against the operation URI template (+ optional HTTP methods). No method modifier matches every method.
     * @param string|null $formFieldType fully-qualified Symfony Form type FQCN override for BO forms
     * @param array<string, mixed>|null $formOptions extra options passed verbatim to the Symfony form type constructor
     * @param list<Constraint>|null $constraints Symfony validation constraints applied to each value before persistence. Null/empty means no validation. Must be serializable (no Callback with a closure).
     * @param string|null $labelWording Translation wording key shown in BO. Required when associatedForms or associatedGrids is set.
     * @param string|null $labelDomain translation domain for label wording
     * @param string|null $descriptionWording translation wording key shown as BO help text
     * @param string|null $descriptionDomain translation domain for description wording
     *
     * @throws InvalidExtraPropertyDefinitionException when entityName or propertyName is empty or not a valid SQL identifier, when associatedForms/associatedGrids have invalid format or duplicates, when labelWording is missing despite being required, or when the computed storage column name exceeds 64 characters
     */
    public function __construct(
        string $entityName,
        protected readonly string $propertyName,
        protected readonly ExtraPropertyType $type = ExtraPropertyType::STRING,
        protected readonly ExtraPropertyScope $scope = ExtraPropertyScope::COMMON,
        ?string $moduleName = null,
        protected readonly ?array $enumValues = null,
        protected readonly int|float|string|bool|null $defaultValue = null,
        protected readonly bool $nullable = true,
        protected readonly bool $required = false,
        protected readonly ?int $size = null,
        protected readonly ExtraPropertySqlIndex $sqlIndex = ExtraPropertySqlIndex::NONE,
        protected readonly bool $displayFront = true,
        protected readonly ?array $associatedForms = null,
        protected readonly ?array $associatedGrids = null,
        protected readonly ?array $associatedApis = null,
        protected readonly ?string $formFieldType = null,
        protected readonly ?array $formOptions = null,
        protected readonly ?array $constraints = null,
        protected readonly ?string $labelWording = null,
        protected readonly ?string $labelDomain = null,
        protected readonly ?string $descriptionWording = null,
        protected readonly ?string $descriptionDomain = null,
    ) {
        // Entity names are SQL identifier fragments (tables + primary key column):
        // normalize to lower snake_case before validating and storing — tableize()
        // converts CamelCase (ProductAttribute → product_attribute), then hyphens
        // become underscores.
        $normalizedEntityName = str_replace('-', '_', Inflector::getInflector()->tableize($entityName));
        if (!ExtraPropertyValidator::isTableOrIdentifier($normalizedEntityName)) {
            throw new InvalidExtraPropertyDefinitionException(sprintf(
                'ExtraPropertyDefinition: entityName "%s" must be a valid SQL identifier ([a-zA-Z0-9_-]+).',
                $entityName
            ));
        }
        $this->entityName = $normalizedEntityName;
        if (!ExtraPropertyValidator::isTableOrIdentifier($propertyName)) {
            throw new InvalidExtraPropertyDefinitionException(sprintf(
                'ExtraPropertyDefinition: propertyName "%s" must be a valid SQL identifier ([a-zA-Z0-9_-]+).',
                $propertyName
            ));
        }

        // Non-empty, non-sentinel module names must match PrestaShop module naming rules.
        // The normalized value is what gets stored: getModuleName() always returns null for core fields.
        $resolvedModuleName = (null === $moduleName || '' === $moduleName || self::CORE_MODULE_KEY === $moduleName)
            ? null
            : $moduleName;
        $this->moduleName = $resolvedModuleName;
        $this->normalizedModuleKey = $resolvedModuleName ?? self::CORE_MODULE_KEY;
        if (null !== $resolvedModuleName && !ExtraPropertyValidator::isModuleName($resolvedModuleName)) {
            throw new InvalidExtraPropertyDefinitionException(sprintf(
                'ExtraPropertyDefinition: moduleName "%s" is not a valid PrestaShop module name.',
                $moduleName
            ));
        }

        // Storage column names must be valid SQL identifiers (1–64 chars; hyphens were
        // already normalized to underscores by buildStorageColumnName()).
        // This is the safety contract DDL consumers (ExtraPropertySchemaManager) rely on:
        // any identifier coming from a constructed definition is safe to embed in SQL.
        $storageColumn = self::buildStorageColumnName($resolvedModuleName, $propertyName);
        if (!ExtraPropertyValidator::isTableOrIdentifier($storageColumn)) {
            throw new InvalidExtraPropertyDefinitionException(sprintf(
                'ExtraPropertyDefinition: computed storage column name "%s" must be a valid SQL identifier (1–64 characters).',
                $storageColumn
            ));
        }

        if (!empty($associatedForms)) {
            $seenFormIds = [];
            foreach ($associatedForms as $entry) {
                $parsed = self::parseFormEntry((string) $entry);
                if ('' === $parsed['formId']) {
                    throw new InvalidExtraPropertyDefinitionException(sprintf(
                        'ExtraPropertyDefinition: invalid associatedForms entry "%s" — formId must not be empty.',
                        $entry
                    ));
                }
                if (isset($seenFormIds[$parsed['formId']])) {
                    throw new InvalidExtraPropertyDefinitionException(sprintf(
                        'ExtraPropertyDefinition: duplicate formId "%s" in associatedForms.',
                        $parsed['formId']
                    ));
                }
                $seenFormIds[$parsed['formId']] = true;
            }
        }

        if (!empty($associatedGrids)) {
            $seenGridIds = [];
            foreach ($associatedGrids as $entry) {
                $parsed = self::parseGridEntry((string) $entry);
                if ('' === $parsed['gridId']) {
                    throw new InvalidExtraPropertyDefinitionException(sprintf(
                        'ExtraPropertyDefinition: invalid associatedGrids entry "%s" — gridId must not be empty.',
                        $entry
                    ));
                }
                if (isset($seenGridIds[$parsed['gridId']])) {
                    throw new InvalidExtraPropertyDefinitionException(sprintf(
                        'ExtraPropertyDefinition: duplicate gridId "%s" in associatedGrids.',
                        $parsed['gridId']
                    ));
                }
                $seenGridIds[$parsed['gridId']] = true;
            }
        }

        if (!empty($associatedApis)) {
            $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
            foreach ($associatedApis as $entry) {
                $entryString = (string) $entry;
                $colonPos = strpos($entryString, ':');
                $rawPath = trim(false !== $colonPos ? substr($entryString, 0, $colonPos) : $entryString);
                if ('' === $rawPath) {
                    throw new InvalidExtraPropertyDefinitionException(sprintf(
                        'ExtraPropertyDefinition: invalid associatedApis entry "%s" — URI path must not be empty.',
                        $entry
                    ));
                }
                $parsedApi = self::parseApiEntry($entryString);
                foreach ($parsedApi['methods'] ?? [] as $method) {
                    if (!in_array($method, $allowedMethods, true)) {
                        throw new InvalidExtraPropertyDefinitionException(sprintf(
                            'ExtraPropertyDefinition: invalid HTTP method "%s" in associatedApis entry "%s" (allowed: %s).',
                            $method,
                            $entry,
                            implode(', ', $allowedMethods)
                        ));
                    }
                }
            }
        }

        if (null !== $constraints) {
            foreach ($constraints as $constraint) {
                if (!$constraint instanceof Constraint) {
                    throw new InvalidExtraPropertyDefinitionException(sprintf(
                        'ExtraPropertyDefinition: every constraint must be a %s instance, got "%s" (entity "%s", property "%s").',
                        Constraint::class,
                        get_debug_type($constraint),
                        $entityName,
                        $propertyName
                    ));
                }
            }
        }

        if ((!empty($associatedForms) || !empty($associatedGrids)) && (null === $labelWording || '' === trim($labelWording))) {
            throw new InvalidExtraPropertyDefinitionException(sprintf(
                'ExtraPropertyDefinition: labelWording is required when associatedForms or associatedGrids is set (entity "%s", property "%s").',
                $entityName,
                $propertyName
            ));
        }
    }

    // -------------------------------------------------------------------------
    // Static factories
    // -------------------------------------------------------------------------

    /**
     * Builds an instance from a raw registry DB row.
     *
     * nullable and enumValues are not persisted in the registry table: the live DB schema of
     * the storage column is their source of truth. The repository injects them into the row
     * under the synthetic 'nullable' and 'enum_values' keys (see
     * ExtraPropertyDefinitionRepository::enrichRowsWithColumnMetadata()). When the keys are
     * absent (e.g. storage column not created yet), safe defaults apply (nullable, no enum).
     *
     * @param array<string, mixed> $row
     *
     * @throws InvalidExtraPropertyDefinitionException when entityName or propertyName is empty in the row
     */
    public static function fromRow(array $row): self
    {
        $formOptions = ($raw = (string) ($row['form_options'] ?? '')) !== ''
            ? json_decode($raw, true)
            : null;

        $associatedForms = array_values(array_filter(
            (array) json_decode((string) ($row['associated_forms'] ?? ''), true),
            static fn (mixed $v): bool => is_string($v) && '' !== $v
        )) ?: null;

        $associatedGrids = array_values(array_filter(
            (array) json_decode((string) ($row['associated_grids'] ?? ''), true),
            static fn (mixed $v): bool => is_string($v) && '' !== $v
        )) ?: null;

        $associatedApis = array_values(array_filter(
            (array) json_decode((string) ($row['associated_apis'] ?? ''), true),
            static fn (mixed $v): bool => is_string($v) && '' !== $v
        )) ?: null;

        $type = ExtraPropertyType::from((string) ($row['type'] ?? ExtraPropertyType::STRING->value));
        $rawDefaultValue = isset($row['default_value']) && '' !== $row['default_value'] ? $row['default_value'] : null;

        return new self(
            entityName: (string) ($row['entity_name'] ?? ''),
            propertyName: (string) ($row['property_name'] ?? ''),
            type: $type,
            scope: ExtraPropertyScope::from((string) ($row['scope'] ?? ExtraPropertyScope::COMMON->value)),
            moduleName: isset($row['module_name']) ? (string) $row['module_name'] : null,
            enumValues: isset($row['enum_values']) && is_array($row['enum_values']) && [] !== $row['enum_values'] ? array_values($row['enum_values']) : null,
            defaultValue: null !== $rawDefaultValue ? ExtraPropertyValueCaster::castFromDb($type, $rawDefaultValue) : null,
            nullable: !array_key_exists('nullable', $row) || (bool) $row['nullable'],
            required: !empty($row['required']),
            size: isset($row['size']) && '' !== $row['size'] ? (int) $row['size'] : null,
            sqlIndex: ExtraPropertySqlIndex::from((string) ($row['sql_index'] ?? ExtraPropertySqlIndex::NONE->value)),
            displayFront: !empty($row['display_front']),
            associatedForms: is_array($associatedForms) ? $associatedForms : null,
            associatedGrids: is_array($associatedGrids) ? $associatedGrids : null,
            associatedApis: is_array($associatedApis) ? $associatedApis : null,
            formFieldType: isset($row['form_field_type']) && '' !== $row['form_field_type'] ? (string) $row['form_field_type'] : null,
            formOptions: is_array($formOptions) ? $formOptions : null,
            constraints: self::decodeConstraints($row['constraints'] ?? null),
            labelWording: isset($row['label_wording']) && '' !== $row['label_wording'] ? (string) $row['label_wording'] : null,
            labelDomain: isset($row['label_domain']) && '' !== $row['label_domain'] ? (string) $row['label_domain'] : null,
            descriptionWording: isset($row['description_wording']) && '' !== $row['description_wording'] ? (string) $row['description_wording'] : null,
            descriptionDomain: isset($row['description_domain']) && '' !== $row['description_domain'] ? (string) $row['description_domain'] : null,
        );
    }

    /**
     * Normalizes the registry "constraints" cell into a list of Constraint objects.
     *
     * Accepts both shapes so fromRow() works for an in-memory row (constraints already given as
     * Constraint objects) and a DB row (constraints serialized to a string):
     *  - array  → already-decoded constraints; filtered and returned as-is (no unserialize).
     *  - string → a serialized blob written by trusted module install code (registerExtraProperty);
     *             unserialized then filtered.
     * Anything that is not a Symfony Constraint is discarded. Returns null when nothing usable
     * remains, mirroring the "no validation" default.
     *
     * @return list<Constraint>|null
     */
    private static function decodeConstraints(mixed $raw): ?array
    {
        if (is_array($raw)) {
            return self::filterConstraints($raw);
        }

        if (!is_string($raw) || '' === $raw) {
            return null;
        }

        $decoded = @unserialize($raw, ['allowed_classes' => true]);

        return is_array($decoded) ? self::filterConstraints($decoded) : null;
    }

    /**
     * @param array<mixed> $candidates
     *
     * @return list<Constraint>|null
     */
    private static function filterConstraints(array $candidates): ?array
    {
        $constraints = array_values(array_filter(
            $candidates,
            static fn (mixed $constraint): bool => $constraint instanceof Constraint
        ));

        return [] !== $constraints ? $constraints : null;
    }

    // -------------------------------------------------------------------------
    // Copy-with methods
    // -------------------------------------------------------------------------

    /**
     * Returns a copy of this definition with the given module name set.
     *
     * Used by Module::registerExtraProperty() to inject the calling module's name
     * when moduleName was left null by the developer.
     */
    public function withModuleName(string $moduleName): self
    {
        return new self(
            entityName: $this->entityName,
            propertyName: $this->propertyName,
            type: $this->type,
            scope: $this->scope,
            moduleName: $moduleName,
            enumValues: $this->enumValues,
            defaultValue: $this->defaultValue,
            nullable: $this->nullable,
            required: $this->required,
            size: $this->size,
            sqlIndex: $this->sqlIndex,
            displayFront: $this->displayFront,
            associatedForms: $this->associatedForms,
            associatedGrids: $this->associatedGrids,
            associatedApis: $this->associatedApis,
            formFieldType: $this->formFieldType,
            formOptions: $this->formOptions,
            constraints: $this->constraints,
            labelWording: $this->labelWording,
            labelDomain: $this->labelDomain,
            descriptionWording: $this->descriptionWording,
            descriptionDomain: $this->descriptionDomain,
        );
    }

    // -------------------------------------------------------------------------
    // Getters
    // -------------------------------------------------------------------------

    /**
     * Entity table name — always lower snake_case (normalized at construction).
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * Returns the primary key column name of the entity ('id_' + entityName) — also the
     * FK column of the *_extra tables. Centralizes the PrestaShop naming convention so
     * callers holding a definition never build it manually.
     */
    public function getPrimaryKeyName(): string
    {
        return 'id_' . $this->entityName;
    }

    /**
     * Owning module technical name — always null for core fields (normalized at construction).
     */
    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getType(): ExtraPropertyType
    {
        return $this->type;
    }

    public function getScope(): ExtraPropertyScope
    {
        return $this->scope;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedApis(): ?array
    {
        return $this->associatedApis;
    }

    /**
     * Returns the parsed Admin API placement entries.
     *
     * @return list<array{path: string, methods: list<string>|null}>
     */
    public function getApiEntries(): array
    {
        if (null === $this->associatedApis) {
            return [];
        }

        return array_map(static fn (string $entry): array => self::parseApiEntry($entry), $this->associatedApis);
    }

    /**
     * Returns true when this definition targets the given Admin API operation, identified by its
     * URI template and HTTP method. Matching is purely URI-template based, so a definition never
     * leaks onto a resource it does not explicitly list. An entry with no method modifier matches
     * every HTTP method on that template.
     */
    public function matchesApi(string $uriTemplate, string $method): bool
    {
        if (null === $this->associatedApis) {
            return false;
        }

        $normalizedTemplate = self::normalizeApiPath($uriTemplate);
        $upperMethod = strtoupper($method);
        foreach ($this->associatedApis as $entry) {
            $parsed = self::parseApiEntry((string) $entry);
            if ($parsed['path'] !== $normalizedTemplate) {
                continue;
            }
            if (null === $parsed['methods'] || in_array($upperMethod, $parsed['methods'], true)) {
                return true;
            }
        }

        return false;
    }

    public function isDisplayFront(): bool
    {
        return $this->displayFront;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return list<Constraint>|null
     */
    public function getConstraints(): ?array
    {
        return $this->constraints;
    }

    public function getFormFieldType(): ?string
    {
        return $this->formFieldType;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getFormOptions(): ?array
    {
        return $this->formOptions;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getSqlIndex(): ExtraPropertySqlIndex
    {
        return $this->sqlIndex;
    }

    /**
     * @return list<string>|null
     */
    public function getEnumValues(): ?array
    {
        return $this->enumValues;
    }

    public function getDefaultValue(): int|float|string|bool|null
    {
        return $this->defaultValue;
    }

    public function getLabelWording(): ?string
    {
        return $this->labelWording;
    }

    public function getLabelDomain(): ?string
    {
        return $this->labelDomain;
    }

    public function getDescriptionWording(): ?string
    {
        return $this->descriptionWording;
    }

    public function getDescriptionDomain(): ?string
    {
        return $this->descriptionDomain;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedForms(): ?array
    {
        return $this->associatedForms;
    }

    /**
     * Returns the fully-resolved placement entry for a specific form, or null if not associated.
     *
     * @return array{formId: string, mode: 'before'|'after'|null, path: string|null, anchor: string|null}|null
     */
    public function getFormEntry(string $formId): ?array
    {
        if (null === $this->associatedForms) {
            return null;
        }
        foreach ($this->associatedForms as $entry) {
            $parsed = self::parseFormEntry($entry);
            if ($parsed['formId'] === $formId) {
                return $parsed;
            }
        }

        return null;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedGrids(): ?array
    {
        return $this->associatedGrids;
    }

    /**
     * Returns the parsed placement entry for a specific grid, or null if not associated.
     *
     * @return array{gridId: string, columnId: string|null, mode: 'before'|'after'|null}|null
     */
    public function getGridEntry(string $gridId): ?array
    {
        if (null === $this->associatedGrids) {
            return null;
        }
        foreach ($this->associatedGrids as $entry) {
            $parsed = self::parseGridEntry($entry);
            if ($parsed['gridId'] === $gridId) {
                return $parsed;
            }
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Naming helpers
    // -------------------------------------------------------------------------

    /**
     * Returns the physical SQL storage column name for this definition.
     */
    public function getStorageColumnName(): string
    {
        return self::buildStorageColumnName($this->moduleName, $this->propertyName);
    }

    /**
     * The property's flat field identifier, used identically by the back-office form (field name), the grid
     * (column id / SELECT alias) and the Admin API (inline list key).
     *
     * A property is unique per module + property name, so the scope is intentionally not part of it — keeping
     * identifiers short and predictable.
     */
    public function getFieldName(): string
    {
        return 'extra_' . $this->normalizedModuleKey . '_' . $this->propertyName;
    }

    /**
     * Returns the module key used in grouped extra-property arrays: the module
     * technical name, or the canonical '_core' key for core fields.
     */
    public function getNormalizedModuleKey(): string
    {
        return $this->normalizedModuleKey;
    }

    /**
     * Returns the name of the extra value table (without DB prefix) for this definition's scope.
     */
    public function getExtraTableName(): string
    {
        return self::buildExtraTableName($this->entityName, $this->scope);
    }

    /**
     * Returns the name of the base entity table (without DB prefix) for this definition's scope.
     *
     * Used by SchemaManager to verify the base table exists before creating the extra table.
     * LANG scope → {entity}_lang, SHOP scope → {entity}_shop, COMMON → {entity}.
     */
    public function getBaseTableName(): string
    {
        return match ($this->scope) {
            ExtraPropertyScope::LANG => $this->entityName . '_lang',
            ExtraPropertyScope::SHOP => $this->entityName . '_shop',
            default => $this->entityName,
        };
    }

    /**
     * Returns the extra value table name for a given entity and scope.
     *
     * Prefer getExtraTableName() whenever a definition instance is available. Use this
     * static version only when none exists: ExtraPropertyWriter::deleteAll() (sweeps all
     * scope tables regardless of registered definitions) and
     * ExtraPropertyDefinitionRepository::enrichRowsWithColumnMetadata() (runs on raw rows
     * before definitions can be constructed).
     *
     * @return string e.g. 'product_extra', 'product_extra_lang', 'product_extra_shop'
     */
    public static function buildExtraTableName(string $entityName, ExtraPropertyScope $scope): string
    {
        return match ($scope) {
            ExtraPropertyScope::LANG => $entityName . '_extra_lang',
            ExtraPropertyScope::SHOP => $entityName . '_extra_shop',
            default => $entityName . '_extra',
        };
    }

    /**
     * Returns the storage column name for a given module and property name.
     *
     * Prefer getStorageColumnName() whenever a definition instance is available. Use this
     * static version only when none exists:
     * ExtraPropertyDefinitionRepository::enrichRowsWithColumnMetadata() (runs on raw rows
     * before definitions can be constructed).
     */
    public static function buildStorageColumnName(?string $moduleName, string $propertyName): string
    {
        // Hyphens are valid in property/entity names but not in unquoted SQL identifiers — normalize them.
        $normalizedProperty = str_replace('-', '_', $propertyName);
        if (null === $moduleName || '' === $moduleName || self::CORE_MODULE_KEY === $moduleName) {
            return $normalizedProperty;
        }

        $normalizedModule = str_replace('-', '_', $moduleName);

        return $normalizedModule . '_' . $normalizedProperty;
    }

    /**
     * Parses one entry from the associated_grids JSON array into its components.
     *
     * Format: "gridId[:columnId[:before|after]]"
     * The ":" separates the grid id from the column id, and the column id from the optional mode.
     * Grid columns are flat (no nesting), so the column id never contains a separator.
     *
     * @return array{gridId: string, columnId: string|null, mode: 'before'|'after'|null}
     */
    protected static function parseGridEntry(string $entry): array
    {
        $colonPos = strpos($entry, ':');
        if (false === $colonPos) {
            return ['gridId' => $entry, 'columnId' => null, 'mode' => null];
        }

        $gridId = substr($entry, 0, $colonPos);
        $rest = substr($entry, $colonPos + 1);

        $mode = 'after';
        foreach ([':before', ':after'] as $suffix) {
            if (str_ends_with($rest, $suffix)) {
                $mode = ltrim($suffix, ':');
                $rest = substr($rest, 0, -strlen($suffix));
                break;
            }
        }

        return ['gridId' => $gridId, 'columnId' => '' !== $rest ? $rest : null, 'mode' => '' !== $rest ? $mode : null];
    }

    /**
     * Parses one entry from the associated_forms JSON array into its fully-resolved components.
     *
     * Format: "formId[:path[:before|after]]"
     * The ":" separates the form id from the field path, and the field path from the optional mode.
     * Nesting *within* the path still uses "." (e.g. "options.suppliers").
     *
     * Placement is resolved here, once, so consumers never re-interpret the entry. path is the form node
     * the field belongs to; it is null (and only then) when there is no path — the signal for fallback:
     * - no path  => fallback section (path/anchor are null).
     * - no mode  => the path is a container; path is the full path, anchor is null
     *               (the field is appended inside that node).
     * - mode set => the last raw segment is an anchor; path is its parent (the raw path minus its last
     *               segment, "" when the anchor is at the root) and anchor is that last segment
     *               (the field is positioned before/after the anchor inside the parent).
     *
     * @return array{formId: string, mode: 'before'|'after'|null, path: string|null, anchor: string|null}
     */
    protected static function parseFormEntry(string $entry): array
    {
        $colonPos = strpos($entry, ':');
        if (false === $colonPos) {
            return ['formId' => $entry, 'mode' => null, 'path' => null, 'anchor' => null];
        }

        $formId = substr($entry, 0, $colonPos);
        $rest = substr($entry, $colonPos + 1);

        $mode = null;
        foreach ([':before', ':after'] as $suffix) {
            if (str_ends_with($rest, $suffix)) {
                $mode = ltrim($suffix, ':');
                $rest = substr($rest, 0, -strlen($suffix));
                break;
            }
        }

        $rawPath = '' !== $rest ? $rest : null;

        if (null === $rawPath) {
            $path = null;
            $anchor = null;
        } elseif (null === $mode) {
            // Container placement: the field is appended inside the full path.
            $path = $rawPath;
            $anchor = null;
        } else {
            // Anchor placement: the field is a sibling of the last raw segment inside its parent.
            $lastDot = strrpos($rawPath, '.');
            $path = false !== $lastDot ? substr($rawPath, 0, $lastDot) : '';
            $anchor = false !== $lastDot ? substr($rawPath, $lastDot + 1) : $rawPath;
        }

        return ['formId' => $formId, 'mode' => $mode, 'path' => $path, 'anchor' => $anchor];
    }

    /**
     * Parses one entry from the associated_apis JSON array.
     *
     * Format: "uriPath[:METHOD[,METHOD...]]"
     * The ":" separates the URI path from an optional comma-separated HTTP method list; URI
     * templates never contain ":", so splitting on the first ":" is unambiguous. With no method
     * list the entry matches every HTTP method on that URI template.
     *
     * @return array{path: string, methods: list<string>|null}
     */
    protected static function parseApiEntry(string $entry): array
    {
        $colonPos = strpos($entry, ':');
        if (false === $colonPos) {
            return ['path' => self::normalizeApiPath($entry), 'methods' => null];
        }

        $path = self::normalizeApiPath(substr($entry, 0, $colonPos));
        $methodsSpec = trim(substr($entry, $colonPos + 1));
        if ('' === $methodsSpec) {
            return ['path' => $path, 'methods' => null];
        }

        $methods = array_values(array_filter(
            array_map(static fn (string $m): string => strtoupper(trim($m)), explode(',', $methodsSpec)),
            static fn (string $m): bool => '' !== $m
        ));

        return ['path' => $path, 'methods' => [] !== $methods ? $methods : null];
    }

    /**
     * Normalizes a URI path for comparison: trims, forces a single leading slash, and drops a
     * trailing slash (except for the root "/").
     */
    protected static function normalizeApiPath(string $path): string
    {
        $normalized = '/' . ltrim(trim($path), '/');

        return '/' !== $normalized ? rtrim($normalized, '/') : $normalized;
    }
}
