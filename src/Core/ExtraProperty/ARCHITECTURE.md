# Extra Property Feature — Architecture

> **Discussion**: https://github.com/PrestaShop/PrestaShop/discussions/40767
>
> This document describes the architecture for a system that allows modules to register extra properties on existing PrestaShop entities without modifying core database tables.
>
> **Note**: this is the implemented version. It diverges from the original proposal on several points — see inline notes marked **[impl]**.

---

## Table of Contents

1. [Overview & Naming Conventions](#1-overview--naming-conventions)
2. [Database Structure](#2-database-structure)
3. [Core Services](#3-core-services)
4. [CQRS Domain Layer (Partial)](#4-cqrs-domain-layer-partial)
5. [Module Integration](#5-module-integration)
6. [ObjectModel Integration (Front-Office)](#6-objectmodel-integration-front-office)
7. [Admin API Integration](#7-admin-api-integration)
8. [Back-Office Form Integration](#8-back-office-form-integration)
9. [Grid Integration](#9-grid-integration)
10. [Supported Types](#10-supported-types)
11. [Performance Considerations](#11-performance-considerations)
12. [Conflict Handling](#12-conflict-handling)
13. [Backward Compatibility](#13-backward-compatibility)
14. [Phased Implementation Plan](#14-phased-implementation-plan)
15. [Testing Strategy](#15-testing-strategy)

---

## 1. Overview & Naming Conventions

### Concept

Modules can register **extra properties** on existing entities (Product, Customer, Order, etc.). These properties are stored in dedicated tables separate from core tables, created dynamically when a module registers its first extra property for an entity.

### Naming


| Concept                    | Convention                                 |
| -------------------------- | ------------------------------------------ |
| **Namespace**              | `PrestaShop\PrestaShop\Core\ExtraProperty` |
| **Directory**              | `src/Core/ExtraProperty/`                  |
| **DB table suffix**        | `_extra`, `_extra_lang`, `_extra_shop`     |
| **Column naming**          | `{module_name}_{field_name}`               |
| **Column name max length** | 64 characters (MariaDB identifier limit)   |


### Scopes

Extra properties support three scopes, mirroring PrestaShop's native multilang/multishop system:


| Scope    | Table                 | Description                                                |
| -------- | --------------------- | ---------------------------------------------------------- |
| `common` | `{entity}_extra`      | Same value across all shops and languages                  |
| `lang`   | `{entity}_extra_lang` | Value varies per language (and per shop if multilang_shop) |
| `shop`   | `{entity}_extra_shop` | Value varies per shop                                      |


> **[impl]** Scope identifiers are lowercase strings (`common`, `lang`, `shop`) matching the string-backed `ExtraPropertyScope` enum values and the `field_scope` ENUM column in the DB.

---

## 2. Database Structure

### 2.1. Definition Registry Table

This table is the central registry of all registered extra properties. It is created during PrestaShop installation (added to `install-dev/data/db_structure.sql`).

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_extra_property_definition` (
  `id_extra_property_definition` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_name` varchar(64) NOT NULL,
  `module_name` varchar(64) NOT NULL DEFAULT '',
  `field_name` varchar(64) NOT NULL,
  `storage_column_name` varchar(64) NOT NULL,
  `field_type` ENUM('int','bool','string','float','date','html','json','choice') NOT NULL,
  `field_scope` ENUM('common','lang','shop') NOT NULL DEFAULT 'common',
  `symfony_field_type` varchar(255) DEFAULT NULL,
  `property_path` varchar(255) DEFAULT NULL,
  `sql_index` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `validator` varchar(64) DEFAULT NULL,
  `choices` text DEFAULT NULL,
  `display_front` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `display_api` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `display_bo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `display_grid` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `grid_position` int(10) unsigned DEFAULT NULL,
  `title_wording` varchar(255) DEFAULT NULL,
  `title_domain` varchar(255) DEFAULT NULL,
  `description_wording` varchar(255) DEFAULT NULL,
  `description_domain` varchar(255) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_extra_property_definition`),
  UNIQUE KEY `entity_module_field` (`entity_name`, `module_name`, `field_name`),
  KEY `entity_name` (`entity_name`),
  KEY `module_name` (`module_name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;
```

**Key fields:**

- `entity_name`: the ObjectModel table name (e.g., `product`, `customer`, `order`)
- `module_name`: the module's technical name (`NOT NULL DEFAULT ''`; empty string = core field)
- `field_name`: the property name within the module (e.g., `custom_size`)
- `storage_column_name`: computed as `{module_name}_{field_name}` (e.g., `mymodule_custom_size`); for core fields: `_{field_name}`
- `field_type`: maps to `ExtraPropertyType` string-backed enum values (`'int'`, `'bool'`, `'string'`, …)
- `field_scope`: maps to `ExtraPropertyScope` string-backed enum values (`'common'`, `'lang'`, `'shop'`)
- `choices`: JSON-encoded array for `ExtraPropertyType::Choice` fields
- `title_wording`, `title_domain`: i18n label for BO (wording + translation domain, resolved at runtime)
- `description_wording`, `description_domain`: i18n description for BO
- `display_front`, `display_api`, `display_bo`, `display_grid`: visibility flags per context
- `grid_position`: column position in BO grids

> **[impl]** The original proposal used `type tinyint` (int-backed) and `scope tinyint` (int-backed), a `column_name` field, and a single `api_visible` flag. The actual implementation uses string-backed MySQL ENUMs for `field_type`/`field_scope`, a `storage_column_name` field, and per-context visibility flags (`display_front`, `display_api`, `display_bo`, `display_grid`). The `module_name` is `NOT NULL DEFAULT ''` (not just `NOT NULL`) so that the UNIQUE KEY works correctly for core fields.

### 2.2. Dynamic Entity Extra Tables

These tables are created dynamically by the `ExtraPropertySchemaManager` when the first extra property is registered for an entity. Columns are added/removed as modules register/unregister properties.

**Common extra table** — `PREFIX_{entity}_extra`:

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_product_extra` (
  `id_product` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;
```

**Lang extra table** — `PREFIX_{entity}_extra_lang`:

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_product_extra_lang` (
  `id_product` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_product`, `id_lang`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;
```

**Shop extra table** — `PREFIX_{entity}_extra_shop`:

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_product_extra_shop` (
  `id_product` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;
```

Columns are added dynamically via `ALTER TABLE ADD COLUMN` when a module registers an extra property. For example:

```sql
ALTER TABLE `PREFIX_product_extra` ADD COLUMN `mymodule_custom_size` varchar(255) DEFAULT NULL;
ALTER TABLE `PREFIX_product_extra_lang` ADD COLUMN `mymodule_custom_label` varchar(255) DEFAULT NULL;
ALTER TABLE `PREFIX_product_extra_shop` ADD COLUMN `mymodule_shop_flag` tinyint(1) DEFAULT 0;
```

### 2.3. Column Type Mapping


| ExtraPropertyType | SQL Column Type                                       |
| ----------------- | ----------------------------------------------------- |
| `int`             | `int(10) DEFAULT NULL`                                |
| `bool`            | `tinyint(1) DEFAULT 0`                                |
| `string`          | `varchar({size}) DEFAULT NULL` (size defaults to 255) |
| `float`           | `decimal(20,6) DEFAULT NULL`                          |
| `date`            | `datetime DEFAULT NULL`                               |
| `html`            | `text DEFAULT NULL`                                   |
| `json`            | `text DEFAULT NULL`                                   |
| `choice`          | `varchar(64) DEFAULT NULL`                            |


---

## 3. Core Services

### 3.1. Directory Structure

```
src/Core/ExtraProperty/
├── ExtraPropertyType.php
├── ExtraPropertyScope.php
├── ExtraPropertyNaming.php              ← new: centralized naming utility
├── ExtraPropertyOptions.php
├── ExtraPropertyDefinitionCollection.php
├── ExtraPropertyScopeGrouper.php
├── Registry/
│   ├── EntityExtraFieldRegistryInterface.php
│   └── ExtraPropertyRegistryInterface.php
├── Repository/
│   └── ExtraPropertyDefinitionRepositoryInterface.php
├── Schema/
│   ├── ExtraPropertySchemaManagerInterface.php
│   └── ColumnDefinitionMapper.php
├── Storage/
│   ├── ExtraPropertyReaderInterface.php
│   ├── ExtraPropertyWriterInterface.php
│   └── ExtraPropertyValueProviderInterface.php
└── (exception classes)

src/Adapter/ExtraProperty/
├── Registry/
│   ├── ExtraPropertyRegistry.php
│   └── CachedExtraPropertyRegistry.php
├── Repository/
│   └── ExtraPropertyDefinitionRepository.php
├── Schema/
│   ├── ExtraPropertySchemaManager.php
│   └── CacheInvalidatingSchemaManager.php
├── Storage/
│   ├── ExtraPropertyReader.php
│   ├── ExtraPropertyWriter.php
│   └── ExtraPropertyValueProvider.php
├── BackOffice/
│   ├── ExtraPropertiesFormBuilderModifier.php
│   ├── ExtraPropertiesFormDataLoader.php
│   ├── ExtraPropertiesFormDataPersister.php
│   └── ExtraPropertiesFormDefinitionProvider.php
└── Grid/
    ├── ExtraPropertiesGridDefinitionModifier.php
    ├── ExtraPropertiesGridDefinitionProvider.php
    └── ExtraPropertiesGridQueryBuilderModifier.php

src/Adapter/Presenter/
├── AbstractLazyArray.php                ← getExtraProperties() + $extraPropertiesLazyArray
└── ExtraPropertiesLazyArray.php         ← factory methods fromObjectModel/fromObjectModelClass
```

### 3.2. ExtraPropertyType

PHP enum for supported field types:

> **[impl]** String-backed (not int-backed). The string values match the MySQL ENUM literals in `field_type`.

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

enum ExtraPropertyType: string
{
    case Int = 'int';
    case Bool = 'bool';
    case String = 'string';
    case Float = 'float';
    case Date = 'date';
    case Html = 'html';
    case Json = 'json';
    case Choice = 'choice';
}
```

`ExtraPropertyType::fromRegisterOption()` accepts either an enum instance or the string literal, for module compatibility.

### 3.3. ExtraPropertyScope

PHP enum for property scope:

> **[impl]** String-backed (not int-backed). The string values match the MySQL ENUM literals in `field_scope`.

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

enum ExtraPropertyScope: string
{
    case Common = 'common';
    case Lang = 'lang';
    case Shop = 'shop';
}
```

### 3.4. ExtraPropertyNaming

> **[impl]** New utility class (not in original proposal) that centralizes all naming conventions, eliminating duplicated private methods across 10+ files.

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

class ExtraPropertyNaming
{
    public const CORE_MODULE_KEY = '_core';

    /** Returns the extra table name: "{entity}_extra[_{scope}]" */
    public static function extraTableName(string $entityName, string $fieldScope): string;

    /** Returns the storage column name: "{module}_{field}" */
    public static function storageColumnName(string $moduleName, string $fieldName): string;

    /** Returns the BO form field name: "extra_{scope}_{module}_{field}" */
    public static function formFieldName(string $moduleName, string $fieldName, string $scope): string;

    /** Normalizes module_name: '' or null → '_core', otherwise returns the value as-is */
    public static function displayModuleKey(?string $moduleName): string;
}
```

### 3.5. ExtraPropertyOptions

DTO for the optional configuration passed to `registerExtraProperty()`. Provides a clear contract with IDE autocompletion.

> **[impl]** The actual fields differ from the original proposal: `type` and `scope` are included (replacing the separate positional parameters on `registerExtraProperty()`), and visibility flags replace `apiVisible`/`apiMapping`.

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

class ExtraPropertyOptions
{
    public function __construct(
        public readonly ExtraPropertyType|string $type = ExtraPropertyType::String,
        public readonly ExtraPropertyScope|string $scope = ExtraPropertyScope::Common,
        public readonly ?string $symfonyFieldType = null,
        public readonly ?string $propertyPath = null,
        public readonly bool $sqlIndex = false,
        public readonly ?string $validator = null,
        public readonly ?array $choices = null,
        public readonly bool $displayFront = false,
        public readonly bool $displayApi = false,
        public readonly bool $displayBo = true,
        public readonly bool $displayGrid = false,
        public readonly ?int $gridPosition = null,
        public readonly ?string $titleWording = null,
        public readonly ?string $titleDomain = null,
        public readonly ?string $descriptionWording = null,
        public readonly ?string $descriptionDomain = null,
    ) {}
}
```

Usage:

```php
$this->registerExtraProperty(
    'product',
    'video_link',
    new ExtraPropertyOptions(
        type: ExtraPropertyType::String,
        scope: ExtraPropertyScope::Lang,
        titleWording: 'Video link',
        titleDomain: 'Modules.Extrafieldproduct.Admin',
        displayFront: true,
        displayApi: true,
        displayBo: true,
        validator: 'isUrl',
    )
);

// Make strings discoverable for BO translation UI
$this->trans('Video link', [], 'Modules.Extrafieldproduct.Admin');
```

### 3.6. ExtraPropertyRegistry

The registry loads all registered definitions from `ps_extra_property_definition`. The interface focuses purely on reading definitions; caching is handled via decoration.

> **[impl]** Registry methods return raw associative arrays (not `ExtraPropertyDefinitionCollection` / `ExtraPropertyDefinition` VOs). Three interfaces exist for different read granularities:
> - `EntityExtraFieldRegistryInterface` — public-facing facade (register/unregister + `getByEntityNameAllScopes()`)
> - `ExtraPropertyRegistryInterface` — full write + read interface (extends the above)
> - `ExtraPropertyDefinitionRepositoryInterface` — read-only repository

**`ExtraPropertyRegistry`** (adapter): orchestrates register/unregister — validates input, calls `ExtraPropertySchemaManager` to create tables/columns, then calls `ExtraPropertyDefinitionRepository` to persist.

**`CachedExtraPropertyRegistry`** (decorator): wraps `ExtraPropertyRegistry` with Symfony `cache.app` (optional, `@?cache.app`) + `FilesystemAdapter` fallback under `%ps_cache_dir%/extra_property_definition`. Guards `CacheItem::tag()` calls against non-tag-aware pools.

**`CacheInvalidatingSchemaManager`** (decorator): wraps `ExtraPropertySchemaManager`, calls cache invalidation on both pools after any DDL mutation.

### 3.7. ColumnDefinitionMapper

Maps `ExtraPropertyType` string values to SQL column definitions. Used by `ExtraPropertySchemaManager`.

### 3.8. ExtraPropertyReader

Reads extra property values from `_extra` tables. Uses `Doctrine\DBAL\Connection`.

**Interface**: `ExtraPropertyReaderInterface`

Key methods:

```php
/**
 * Returns values grouped by module: ['_core' => ['field' => value], 'mymodule' => [...]]
 */
public function getExtraProperties(
    string $entityName,
    string $primaryKeyName,
    int $entityId,
    ?int $langId = null,
    ?int $shopId = null,
    bool $isLangMultishop = false,
    bool $displayFrontOnly = false,
    ?array $preloadedDefinitionRows = null,
): array;

/**
 * Returns definitions for a given entity, filtered by module and/or scope.
 */
public function getDefinitionsByModule(string $entityName, ?string $moduleName, ?string $fieldScope = null): array;
```

> **[impl]** The original proposal returned a flat `['column_name' => value]` map. The actual implementation returns values **grouped by module**: `['_core' => ['field' => value], 'mymodule' => ['field' => value]]`. The method also accepts `primaryKeyName` (not assumed from entity name), `isLangMultishop`, `displayFrontOnly`, and `preloadedDefinitionRows` (to avoid double DB reads when definitions are already loaded).

**Performance**: reads definitions from the registry (cached); if no definitions exist for the entity, returns an empty array immediately without DB query.

### 3.9. ExtraPropertyWriter

Writes extra property values to `_extra` tables. Uses `INSERT ... ON DUPLICATE KEY UPDATE` for upsert behavior.

**Interface**: `ExtraPropertyWriterInterface`

Key methods:

```php
/**
 * Bulk write: entity values + all lang rows + shop row in one call.
 */
public function writeAll(
    string $entityName,
    string $primaryKeyName,
    int $entityId,
    array $entityValues,
    array $langValuesByIdLang,
    array $shopValues,
    ?int $shopId = null,
): void;

/**
 * Single-field write for a specific scope.
 */
public function writeValue(
    string $entityName,
    string $primaryKeyName,
    int $entityId,
    string $storageColumnName,
    mixed $value,
    string $fieldScope = 'common',
    ?int $langId = null,
    ?int $shopId = null,
): bool;

/**
 * Deletes all extra property rows for one entity instance (all three scopes).
 * Safe to call even if no extra properties are registered — tables that do not
 * exist are silently skipped.
 */
public function deleteAll(string $entityName, string $primaryKeyName, int $entityId): void;
```

> **[impl]** The original proposal named the methods `saveExtraProperties()` / `deleteExtraProperties()`. The actual implementation uses `writeAll()` (bulk, scope-separated), `writeValue()` (single field), and `deleteAll()` (cleans up all three `*_extra*` tables when an entity is deleted). `ObjectModel::delete()` calls `deleteAll()` via `ServiceLocator`, mirroring the pattern used in `persistExtraProperties()`.

### 3.10. ExtraPropertyDefinitionRepository

CRUD operations for the `ps_extra_property_definition` table. Uses `Doctrine\DBAL\Connection`.

Key methods exposed via `ExtraPropertyDefinitionRepositoryInterface`:

- `getByEntityNameAllScopes(string $entityName): array` — returns raw rows for all scopes
- `findDefinitionByModuleAndField(string $entity, string $module, string $field, string $scope): ?array`
- `save(array $definitionData): int`
- `delete(int $id): void`

### 3.11. Service Configuration

Located at `src/PrestaShopBundle/Resources/config/services/adapter/extra_property.yml`:

> **[impl]** Services live under the `Adapter` namespace (not `Core`), with FQCNs that include the subdirectory segment (e.g. `Adapter\ExtraProperty\Storage\ExtraPropertyWriter`). `cache.app` is optional (`@?cache.app`); a `FilesystemAdapter` fallback is registered as `prestashop.extra_property.definition.filesystem_cache`.

```yaml
services:
  _defaults:
    public: false

  # Repository
  PrestaShop\PrestaShop\Adapter\ExtraProperty\Repository\ExtraPropertyDefinitionRepository:
    arguments:
      $connection: '@doctrine.dbal.default_connection'
      $prefix: '%database_prefix%'

  # Registry: base + cache decorator
  PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertyRegistry:
    arguments:
      $repository: '@PrestaShop\PrestaShop\Adapter\ExtraProperty\Repository\ExtraPropertyDefinitionRepository'
      $schemaManager: '@PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertySchemaManagerInterface'

  PrestaShop\PrestaShop\Adapter\ExtraProperty\CachedExtraPropertyRegistry:
    arguments:
      $inner: '@PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertyRegistry'
      $cache: '@?cache.app'
      $filesystemCache: '@prestashop.extra_property.definition.filesystem_cache'

  PrestaShop\PrestaShop\Core\ExtraProperty\EntityExtraFieldRegistryInterface:
    alias: 'PrestaShop\PrestaShop\Adapter\ExtraProperty\CachedExtraPropertyRegistry'

  # Filesystem cache fallback (for FO contexts without cache.app)
  prestashop.extra_property.definition.filesystem_cache:
    class: Symfony\Component\Cache\Adapter\FilesystemAdapter
    arguments:
      $namespace: ''
      $defaultLifetime: 0
      $directory: '%ps_cache_dir%/extra_property_definition'

  # Schema manager: base + cache-invalidating decorator
  PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertySchemaManager:
    arguments:
      $connection: '@doctrine.dbal.default_connection'
      $prefix: '%database_prefix%'

  PrestaShop\PrestaShop\Adapter\ExtraProperty\CacheInvalidatingSchemaManager:
    arguments:
      $inner: '@PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertySchemaManager'
      $cachedRegistry: '@PrestaShop\PrestaShop\Adapter\ExtraProperty\CachedExtraPropertyRegistry'

  PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertySchemaManagerInterface:
    alias: 'PrestaShop\PrestaShop\Adapter\ExtraProperty\CacheInvalidatingSchemaManager'

  # Reader / Writer
  PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyReaderInterface:
    alias: 'PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertyReader'

  PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertyReader:
    arguments:
      $repository: '@PrestaShop\PrestaShop\Core\ExtraProperty\Repository\ExtraPropertyDefinitionRepositoryInterface'
      $connection: '@doctrine.dbal.default_connection'
      $prefix: '%database_prefix%'

  PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertyWriterInterface:
    alias: 'PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertyWriter'

  PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertyWriter:
    arguments:
      $connection: '@doctrine.dbal.default_connection'
      $prefix: '%database_prefix%'

  # Value provider (FO presenters)
  PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertyValueProviderInterface:
    alias: 'PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertyValueProvider'

  PrestaShop\PrestaShop\Adapter\ExtraProperty\ExtraPropertyValueProvider:
    arguments:
      $registry: '@PrestaShop\PrestaShop\Core\ExtraProperty\EntityExtraFieldRegistryInterface'
      $reader: '@PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertyReaderInterface'
```

---

## 4. CQRS Domain Layer (Partial)

Only value read/write operations that need API exposure use the CQRS pattern. Registration/unregistration is handled via direct service calls (see [Module Integration](#5-module-integration)).

### 4.1. Directory Structure

```
src/Core/Domain/ExtraProperty/
├── Command/
│   └── UpdateExtraPropertyValuesCommand.php
├── CommandHandler/
│   └── UpdateExtraPropertyValuesCommandHandlerInterface.php
├── Query/
│   ├── GetExtraPropertyDefinitions.php
│   └── GetExtraPropertyValues.php
├── QueryHandler/
│   ├── GetExtraPropertyDefinitionsHandlerInterface.php
│   └── GetExtraPropertyValuesHandlerInterface.php
├── QueryResult/
│   ├── ExtraPropertyDefinitionInfo.php
│   └── ExtraPropertyValuesResult.php
└── Exception/
    └── ExtraPropertyDomainException.php
```

> **[impl]** `ValueObject/ExtraPropertyId.php` was not created — no use case required a typed ID value object in the implemented handlers.

Adapter implementations in:

```
src/Adapter/ExtraProperty/
├── CommandHandler/
│   └── UpdateExtraPropertyValuesCommandHandler.php
└── QueryHandler/
    ├── GetExtraPropertyDefinitionsHandler.php
    └── GetExtraPropertyValuesHandler.php
```

### 4.2. Commands & Queries

`**UpdateExtraPropertyValuesCommand**`: Takes entity name, entity ID, associative array of `column_name => value`, optional lang ID and shop ID. Used by API write processors and BO form handlers.

> **[impl]** The actual command carries three separate scope arrays instead of a single flat map: `entityValues` (common scope), `langValuesByIdLang` (`[id_lang => [col => value]]`), `shopValuesByShopId` (`[id_shop => [col => value]]`), and an optional `langShopId` for lang-multishop context. This avoids ambiguity between lang and shop keys and matches the `ExtraPropertyWriterInterface::writeAll()` signature exactly.

`**GetExtraPropertyDefinitions**`: Query to list definitions, filterable by entity name and/or module name. Used by the Admin API to list available extra properties.

`**GetExtraPropertyValues**`: Query to read values for a specific entity instance. Used by the Admin API to include extra properties in entity responses.

> **[impl]** `GetExtraPropertyValues` does not accept `langId`/`shopId` parameters. The handler (`GetExtraPropertyValuesHandler`) always loads **all** languages and **all** shops for the entity in a single pass — which is the pattern required by the Admin API. A `displayApiOnly: bool` flag restricts the result to definitions with `display_api = 1`. Lang-scope values are returned indexed by `id_lang` (int); `ExtraPropertiesApiService` converts them to locale strings (e.g. `"fr-FR"`) for the API response.

> **[impl]** `ObjectModel` still calls `ExtraPropertyWriterInterface` directly via `ServiceLocator` (legacy context, unchanged). The CQRS bus is the entry point for `ExtraPropertiesApiService` (Admin API) and `ExtraPropertiesFormDataPersister` (BO forms).

---

## 5. Module Integration

### 5.1. New Methods on Module Class

File: `classes/module/Module.php`

#### `registerExtraProperty()`

> **[impl]** The signature takes a single `ExtraPropertyOptions|array` argument instead of separate `ExtraPropertyType $type` + `ExtraPropertyScope $scope` positional parameters. Type and scope are properties of `ExtraPropertyOptions`.

```php
/**
 * Register an extra property for an entity.
 *
 * @param string $entityName Entity table name (e.g., 'product', 'customer')
 * @param string $fieldName Field name (will be prefixed with module name)
 * @param ExtraPropertyOptions|array $options Configuration DTO or legacy array
 *
 * @return bool
 */
public function registerExtraProperty(
    string $entityName,
    string $fieldName,
    ExtraPropertyOptions|array $options = [],
): bool
```

This method calls core services directly (no CommandBus):

1. Resolves `$this->name` as the module name
2. Validates column name length: `strlen($this->name . '_' . $fieldName) <= 64`
3. Normalizes options: resolves `ExtraPropertyType::fromRegisterOption()` and `ExtraPropertyScope` from options
4. Calls `EntityExtraFieldRegistryInterface::register()` which orchestrates schema creation and definition persistence (cache invalidation is handled by the `CacheInvalidatingSchemaManager` decorator)

#### `unregisterExtraProperty()`

```php
/**
 * Unregister an extra property for an entity.
 *
 * @param string $entityName Entity table name
 * @param string $fieldName Field name (without module prefix)
 * @param string $fieldScope Scope ('common', 'lang', 'shop')
 * @param bool $dropColumn Whether to DROP the column from the extra table
 *
 * @return bool
 */
public function unregisterExtraProperty(
    string $entityName,
    string $fieldName,
    string $fieldScope = 'common',
    bool $dropColumn = false,
): bool
```

#### `unregisterExtraPropertyById()`

```php
public function unregisterExtraPropertyById(int $idExtraPropertyDefinition, bool $dropColumn = false): bool
```

#### `unregisterAllExtraProperties()`

Called internally during `uninstall()`. Queries the registry for all definitions belonging to `$this->name` and unregisters each one.

### 5.2. Automatic Cleanup on Uninstall

In `Module::uninstall()`, before existing cleanup logic:

```php
$this->unregisterAllExtraProperties();
```

### 5.3. Module Usage Example

```php
$this->registerExtraProperty(
    'product',
    'video_link',
    new ExtraPropertyOptions(
        type: ExtraPropertyType::String,
        scope: ExtraPropertyScope::Lang,
        titleWording: 'Video link',
        titleDomain: 'Modules.Extrafieldproduct.Admin',
        descriptionWording: 'Video URL per language',
        descriptionDomain: 'Modules.Extrafieldproduct.Admin',
        displayFront: true,
        displayApi: true,
        displayBo: true,
        validator: 'isUrl'
    )
);

// Make strings discoverable by the BO translation UI
$this->trans('Video link', [], 'Modules.Extrafieldproduct.Admin');
$this->trans('Video URL per language', [], 'Modules.Extrafieldproduct.Admin');
```

### 5.4. Handling Two Modules on the Same Entity

When two modules register extra properties on the same entity (e.g., `product`), they share the same `product_extra` table but have distinct column names due to the `{module_name}_` prefix:

```
product_extra table:
| id_product | moduleA_custom_size | moduleB_is_organic |
|------------|---------------------|--------------------|
| 1          | "XL"                | 1                  |
| 2          | "M"                 | 0                  |
```

Each module only reads/writes its own columns. Uninstalling one module removes only its columns.

---

## 6. ObjectModel Integration (Front-Office)

### 6.1. ExtraPropertiesLazyArray

> **[impl]** `ExtraPropertiesLazyArray` lives in `src/Adapter/Presenter/` (not `src/Core/ExtraProperty/`). It does **not** extend `AbstractLazyArray` directly — it is a collaborator assigned to a protected property `$extraPropertiesLazyArray` on `AbstractLazyArray`. The lazy-load method `getExtraProperties()` is defined once on `AbstractLazyArray` and delegates to this collaborator.
>
> `ExtraPropertiesLazyArray` exposes two factory methods that resolve entity metadata from `ObjectModel::getDefinition()`:

```php
namespace PrestaShop\PrestaShop\Adapter\Presenter;

class ExtraPropertiesLazyArray
{
    /** For array-based data (e.g. product from presenter): resolves pk from ObjectModel static def */
    public static function fromObjectModelClass(
        string $objectModelClass,
        int $entityId,
        ExtraPropertyValueProviderInterface $provider,
        Context $context,
    ): self;

    /** For an already-loaded ObjectModel instance (e.g. Order) */
    public static function fromObjectModel(
        ObjectModel $object,
        ExtraPropertyValueProviderInterface $provider,
        Context $context,
    ): self;

    /** Called by AbstractLazyArray::getExtraProperties() */
    public function getValues(): array;
}
```

`getValues()` delegates to `ExtraPropertyValueProviderInterface::getFrontExtraProperties()`, which in turn calls `ExtraPropertyReaderInterface::getExtraProperties()` with `displayFrontOnly=true`.

**LazyArrays that expose `extraProperties`** (via `$this->extraPropertiesLazyArray` assignment before `parent::__construct()`):

- `ProductLazyArray`
- `CategoryLazyArray`, `SupplierLazyArray`, `ManufacturerLazyArray`, `StoreLazyArray`
- `OrderLazyArray`, `OrderDetailLazyArray`, `OrderReturnLazyArray`

`CartLazyArray` does **not** expose `extraProperties` (left as `null`).

### 6.2. ObjectModel Integration

File: `classes/ObjectModel.php`

> **[impl]** ObjectModel does **not** expose a public `$extra_properties` field. Instead it exposes read/write methods. Extra properties are read/written via `ExtraPropertyReaderInterface` / `ExtraPropertyWriterInterface` obtained through `ServiceLocator`.

Key methods:

```php
/**
 * Get all extra properties grouped by module: ['_core' => [...], 'mymodule' => [...]]
 */
public function getExtraProperties(): array

/**
 * Get extra properties for a specific module.
 */
public function getExtraPropertiesByModule(?string $moduleName): array

/**
 * Get a single extra property value.
 */
public function getExtraProperty(?string $moduleName, string $propertyName, ?string $scope = null): mixed

/**
 * Set a single extra property in memory. Persisted on save().
 */
public function setExtraProperty(?string $moduleName, string $propertyName, mixed $value, ?string $scope = null): void
```

Persistence is handled by `persistExtraProperties()` (called from `add()`/`update()`) which delegates to `ExtraPropertyWriterInterface::writeAll()`.

### 6.3. Lazy Loading

Extra properties are NOT loaded in the ObjectModel constructor. `getExtraProperties()` queries the DB on first call. If no extra properties are registered for the entity, an empty array is returned immediately without any DB query.

### 6.4. Automatic Persistence

In `add()` and `update()`, after the `actionObject*After` hooks, `persistExtraProperties()` is called to flush any values set via `setExtraProperty()`.

### 6.5. Front-Office Template Access

Since `extraProperties` is exposed on all relevant LazyArrays, it is automatically available in FO templates:

**In Smarty templates** (via presenter):

```smarty
{$product.extraProperties.ps_extrafield_product.video_link|default:''}
{$category.extraProperties.ps_extrafield_category.theme_color|default:''}
{$order.extraProperties.ps_extrafield_order.is_priority|default:false}
```

**In PHP** (any ObjectModel):

```php
$product->getExtraProperty('mymodule', 'custom_size');
```

---

## 7. Admin API Integration

### 7.1. Strategy

Extra properties are exposed as an `extraProperties` sub-object in entity API responses, grouped by module name. This clearly distinguishes native fields from extra ones and avoids naming conflicts.

Example API response:

```json
{
  "productId": 1,
  "name": "T-shirt",
  "price": "19.99",
  "extraProperties": {
    "mymodule": {
      "custom_size": "XL",
      "custom_label": "Limited Edition"
    },
    "othermodule": {
      "is_organic": true
    }
  }
}
```

> **[impl]** The response format nests fields under their module name (`extraProperties.{module}.{field}`) rather than flattening all fields under `extraProperties`. Only properties with `display_api = 1` are included.

### 7.2. Implementation

> **[impl]** There is no CQRS layer. The API integration uses a single service `ExtraPropertiesApiService` (in `src/PrestaShopBundle/ApiPlatform/ExtraProperties/`) called from `CQRSApiSerializer` during `normalize()` and `denormalize()`.

`ExtraPropertiesApiService` responsibilities:

- **Read**: `loadExtraProperties(entity, id)` → calls `loadEntityScopeFields()`, `loadLangScopeFields()`, `loadShopScopeFields()` using DBAL queries filtered by `display_api = 1`
- **Write**: `persistExtraProperties(...)` → calls `ExtraPropertyWriterInterface::writeAll()` for entity scope, and per-shop calls for shop scope
- **Validation**: `validateExtraPropertiesPayload()` — checks that submitted field names exist in the registry and have `display_api = 1`

### 7.3. Write Operations

```
PATCH /api/products/1
{
  "extraProperties": {
    "mymodule": {
      "custom_size": "M"
    }
  }
}
```

### 7.4. API Resource Mapping

The definition's `field_name` is used as-is in the API. For core fields (`module_name = ''`), they appear under the `_core` key in the response.

---

## 8. Back-Office Form Integration

### 8.1. Strategy

Extra property fields are added to BO entity forms via the existing hook system. Instead of a single `ExtraPropertyFormHelper`, the implementation uses dedicated modifier/loader/persister services that are wired to the existing Symfony form event system.

> **[impl]** There is no `ExtraPropertyFormHelper`. The original proposal's single-helper approach was replaced by a set of specialized services:
> - `ExtraPropertiesFormBuilderModifier` — adds form fields
> - `ExtraPropertiesFormDataLoader` — loads existing values
> - `ExtraPropertiesFormDataPersister` — persists submitted values (delegates to `ExtraPropertyWriterInterface::writeAll()`)
> - `ExtraPropertiesFormDefinitionProvider` — provides filtered definitions (display_bo = 1)

### 8.2. Type Mapping (ExtraPropertyType → Symfony FormType)

If `symfony_field_type` is set in the definition, it is used directly. Otherwise, the default mapping applies:

| ExtraPropertyType | Symfony Form Type       | Notes                                |
| ----------------- | ----------------------- | ------------------------------------ |
| `int`             | `IntegerType`           |                                      |
| `bool`            | `SwitchType`            | PrestaShop's custom switch form type |
| `string`          | `TextType`              |                                      |
| `float`           | `NumberType`            |                                      |
| `date`            | `DateTimePickerType`    | PrestaShop's custom date picker      |
| `html`            | `FormattedTextareaType` | PrestaShop's TinyMCE textarea        |
| `json`            | `TextareaType`          |                                      |
| `choice`          | `ChoiceType`            | `choices` from definition            |

For `lang` fields, the form type is wrapped in `TranslatableType`.

### 8.3. i18n Labels

Labels and descriptions are stored as wording + domain in the registry. They are resolved at runtime via the Symfony translator:

```
ExtraPropertiesFormBuilderModifier::apply(...)
  └─ $translator->trans($definition['title_wording'], [], $definition['title_domain'])
```

Modules must call `$this->trans(...)` (or provide `.xlf` files) to make wordings discoverable in the BO translation UI.

### 8.4. Basic Integration (via form_rest)

Fields added via `ExtraPropertiesFormBuilderModifier` are automatically rendered by `form_rest()` or `form_end()` calls in Twig templates. Extra properties appear at the end of the form by default.

### 8.5. Module Usage in Form Hooks

> **[impl]** Core handles form building, data loading, and persistence automatically via `ExtraPropertiesFormBuilderModifier`, `ExtraPropertiesFormDataLoader`, and `ExtraPropertiesFormDataPersister` (wired to the entity's form hooks in `services.yml`). Modules do not need to implement `hookActionProductFormBuilderModifier` themselves unless they need custom positioning or custom form types.

---

## 9. Grid Integration

### 9.1. Strategy

Extra property columns are added to BO grids via dedicated modifier services. These services are wired to the existing grid definition/query builder modifier hooks.

> **[impl]** There is no `ExtraPropertyGridHelper`. The original proposal's single-helper approach was replaced by:
> - `ExtraPropertiesGridDefinitionModifier` — adds columns and filters (`display_grid = 1`)
> - `ExtraPropertiesGridQueryBuilderModifier` — adds LEFT JOINs and SELECT aliases
> - `ExtraPropertiesGridDefinitionProvider` — provides filtered definitions (display_grid = 1)

### 9.2. Query Builder Modification

The grid modifier adds LEFT JOINs to the existing search query:

```sql
SELECT p.*, extra.mymodule_custom_size AS extra_common_mymodule_custom_size
FROM ps_product p
LEFT JOIN ps_product_extra extra ON extra.id_product = p.id_product
WHERE ...
ORDER BY extra_common_mymodule_custom_size ASC
```

> **[impl]** SELECT aliases follow the naming convention: `extra_{scope}_{module}_{field}` (generated by `ExtraPropertyNaming::formFieldName()`). This ensures column IDs are unique across scopes and modules.

### 9.3. Column Type Mapping


| ExtraPropertyType | Grid Column Type      |
| ----------------- | --------------------- |
| `int`, `float`    | `DataColumn`          |
| `bool`            | `ToggleColumn`        |
| `string`, `html`  | `DataColumn`          |
| `date`            | `DateTimeColumn`      |
| `choice`          | `DataColumn`          |
| `json`            | Not displayed in grid |


---

## 10. Supported Types

### Phase 2 (Initial types)


| Type    | Constant  | ObjectModel equiv.         | Description                        |
| ------- | --------- | -------------------------- | ---------------------------------- |
| Boolean | `'bool'`  | `ObjectModel::TYPE_BOOL`   | true/false, stored as `tinyint(1)` |
| Integer | `'int'`   | `ObjectModel::TYPE_INT`    | Whole numbers                      |
| String  | `'string'`| `ObjectModel::TYPE_STRING` | Text up to `size` characters       |


### Phase 7 (Additional types)


| Type     | Constant   | Description                                                                  |
| -------- | ---------- | ---------------------------------------------------------------------------- |
| Float    | `'float'`  | Decimal numbers, stored as `decimal(20,6)`                                   |
| DateTime | `'date'`   | Date and time values                                                         |
| Choice   | `'choice'` | Enum-like, configured with `choices` array (similar to Symfony `ChoiceType`) |
| JSON     | `'json'`   | Arbitrary JSON data, auto `json_encode`/`json_decode` on read/write          |


### HTML type


| Type | Constant  | Description                                           |
| ---- | --------- | ----------------------------------------------------- |
| HTML | `'html'`  | Rich text content, purified via `Tools::purifyHTML()` |


---

## 11. Performance Considerations

1. **Registry caching**: The `CachedExtraPropertyRegistry` uses Symfony's `cache.app` pool with a `FilesystemAdapter` fallback for FO legacy contexts. Cache is invalidated when definitions change (register/unregister) via `CacheInvalidatingSchemaManager`.
2. **Lazy loading in ObjectModel**: Extra properties are NOT loaded on object construction. They are loaded on first `getExtraProperties()` / `getExtraProperty()` call.
3. **No-op when unused**: The reader checks definitions first. If no extra properties exist for an entity, an empty array is returned immediately without DB query.
4. **Bulk reading in grids**: `ExtraPropertiesGridQueryBuilderModifier` adds LEFT JOINs to existing grid queries — extra properties are fetched alongside main entity data in a single query (no N+1 problem).
5. **Column-based storage**: Unlike WordPress-style meta tables (one row per meta value), extra properties are stored as columns. This enables SQL indexing and constraints, no row multiplication, and reduced JOINs.
6. **Optional column indexing**: For extra properties used as grid filters, the schema manager can add an index if `sql_index = true` in the definition.
7. **Definition cache across requests**: The `FilesystemAdapter` fallback ensures definitions are not re-read from DB on every FO request even when `cache.app` is not available.

---

## 12. Conflict Handling

1. **Column name uniqueness**: The column name `{module_name}_{field_name}` is enforced unique per entity via the DB unique key `entity_module_field`. For core fields, `module_name = ''` so core columns are `_{field_name}`.
2. **Module name uniqueness**: Module names are guaranteed unique in the `ps_module` table.
3. **Column name length**: Enforced to be <= 64 characters. `registerExtraProperty()` throws an exception if exceeded.
4. **MariaDB limits**: ~1000 columns per table (65 KB row size limit). In practice, this allows hundreds of extra properties per entity.
5. **Type changes**: A module must unregister and re-register to change a field's type. The registry checks for conflicts on `register()`.

---

## 13. Backward Compatibility

1. **No core table modifications**: All extra properties are stored in separate `_extra` tables.
2. **ObjectModel is extended, not broken**: New methods (`getExtraProperties()`, `setExtraProperty()`) are additive. Existing code is completely unaffected.
3. **Module opt-in**: Extra properties only exist when a module registers them. Zero overhead for shops without modules using this feature.
4. **API opt-in**: The `extraProperties` field in API responses is absent by default and only populated for entities that have registered extra properties.
5. **Standard hook integration**: Grid and form integration uses existing, stable hook mechanisms.

---

## 14. Phased Implementation Plan

### Phase 1 — POC: Validate DB Structure ✓

- `ExtraPropertyType`, `ExtraPropertyScope` string-backed enums
- `ColumnDefinitionMapper`
- `ExtraPropertySchemaManager` (create/alter/drop tables)
- `ExtraPropertyDefinitionRepository` (CRUD for definitions)
- `ps_extra_property_definition` table in `install-dev/data/db_structure.sql`

### Phase 2 — Module Methods + Example Module ✓

- `ExtraPropertyRegistry` service (loads and caches definitions)
- `Module::registerExtraProperty()`, `Module::unregisterExtraProperty()`, `Module::unregisterAllExtraProperties()`
- `CachedExtraPropertyRegistry` with `FilesystemAdapter` fallback
- `ExtraPropertyNaming` utility
- Example modules (`modules/ps_extrafield_*/`)

### Phase 3 — FO Integration (ObjectModel) ✓

- `ExtraPropertyReader` and `ExtraPropertyWriter` services
- `ObjectModel` API (`getExtraProperties()`, `getExtraProperty()`, `setExtraProperty()`, `persistExtraProperties()`)
- `ExtraPropertiesLazyArray` + `ExtraPropertyValueProvider`
- FO LazyArrays: `ProductLazyArray`, `CategoryLazyArray`, `OrderLazyArray`, etc.

### Phase 4 — Admin API Integration ✓

- `ExtraPropertiesApiService` (no CQRS in this PR)
- `CQRSApiSerializer` integration
- `display_api` visibility flag
- Module-grouped response format (`extraProperties.{module}.{field}`)

### Phase 5 — BO Form Integration ✓

- `ExtraPropertiesFormBuilderModifier`, `ExtraPropertiesFormDataLoader`, `ExtraPropertiesFormDataPersister`
- `ExtraPropertiesFormDefinitionProvider`
- Type mapping (`ExtraPropertyType` → Symfony `FormType`)
- `TranslatableType` wrapping for `lang` fields
- i18n via wording/domain runtime translation

### Phase 6 — Grid Integration ✓

- `ExtraPropertiesGridDefinitionModifier`, `ExtraPropertiesGridQueryBuilderModifier`
- `ExtraPropertiesGridDefinitionProvider`
- LEFT JOIN query modification
- Toggle route (`admin_common_extra_properties_toggle`)

### Phase 7 — Additional Types ✓

Included in initial implementation: `float`, `date`, `choice`, `json`, `html`.

### Phase 8 — Advanced BO Integration (future)

- Custom form type class (`symfony_field_type` already supported in DB)
- `property_path` for exact form field positioning
- Custom Twig form themes for extra property fields

### Phase 9 — Native BO Module for No-Code Management (future)

- Native module for administrators to create/manage extra properties from the BO without writing code

---

## 15. Testing Strategy

### Unit Tests

- `ExtraPropertyNaming` conventions
- `ColumnDefinitionMapper` type-to-SQL mapping
- `ExtraPropertyRegistry` caching behavior
- `ExtraPropertyScopeGrouper` grouping logic

### Integration Tests

- Full lifecycle: register → create entity with extras → read → update → unregister
- Multi-module coexistence on same entity
- Module uninstall cleanup
- Schema manager table/column creation and removal
- ObjectModel `add()`/`update()` with extra properties

### Functional Tests (via Behat or similar)

- Admin API CRUD with extra properties
- BO form display and submission
- Grid display, sorting, filtering
- FO display via LazyArray / presenter
