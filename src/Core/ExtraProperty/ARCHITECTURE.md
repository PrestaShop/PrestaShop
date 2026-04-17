# Extra Property Feature — Architecture

> **Discussion**: https://github.com/PrestaShop/PrestaShop/discussions/40767
>
> This document describes the architecture for a system that allows modules to register extra properties on existing PrestaShop entities without modifying core database tables.

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

---

## 1. Overview & Naming Conventions

### Concept

Modules can register **extra properties** on existing entities (Product, Customer, Order, etc.). These properties are stored in dedicated tables separate from core tables, created dynamically when a module registers its first extra property for an entity.

### Naming

| Concept | Convention |
|---------|-----------|
| **Namespace** | `PrestaShop\PrestaShop\Core\ExtraProperty` |
| **Directory** | `src/Core/ExtraProperty/` |
| **DB table suffix** | `_extra`, `_extra_lang`, `_extra_shop` |
| **Column naming** | `{module_name}_{field_name}` |
| **Column name max length** | 64 characters (MariaDB identifier limit) |

### Scopes

Extra properties support three scopes, mirroring PrestaShop's native multilang/multishop system:

| Scope | Table | Description |
|-------|-------|-------------|
| `Common` | `{entity}_extra` | Same value across all shops and languages |
| `Lang` | `{entity}_extra_lang` | Value varies per language (and per shop if multilang_shop) |
| `Shop` | `{entity}_extra_shop` | Value varies per shop |

---

## 2. Database Structure

### 2.1. Definition Registry Table

This table is the central registry of all registered extra properties. It is created during PrestaShop installation (added to `install-dev/data/db_structure.sql`).

```sql
CREATE TABLE IF NOT EXISTS `PREFIX_extra_property_definition` (
  `id_extra_property_definition` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_name` varchar(64) NOT NULL,
  `module_name` varchar(64) NOT NULL,
  `field_name` varchar(64) NOT NULL,
  `column_name` varchar(64) NOT NULL,
  `type` tinyint(2) unsigned NOT NULL,
  `scope` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `required` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `default_value` varchar(255) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  `validate` varchar(64) DEFAULT NULL,
  `choices` text DEFAULT NULL,
  `api_visible` tinyint(1) unsigned NOT NULL DEFAULT 1,
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
- `module_name`: the module's technical name (e.g., `mymodule`)
- `field_name`: the property name within the module (e.g., `custom_size`)
- `column_name`: computed as `{module_name}_{field_name}` (e.g., `mymodule_custom_size`)
- `type`: maps to `ExtraPropertyType` enum backed values
- `scope`: maps to `ExtraPropertyScope` enum backed values
- `choices`: JSON-encoded array for `ExtraPropertyType::Choice` fields

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

| ExtraPropertyType | SQL Column Type |
|---|---|
| `Int` | `int(10) DEFAULT NULL` |
| `Bool` | `tinyint(1) DEFAULT 0` |
| `String` | `varchar({size}) DEFAULT NULL` (size defaults to 255) |
| `Float` | `decimal(20,6) DEFAULT NULL` |
| `Date` | `datetime DEFAULT NULL` |
| `Html` | `text DEFAULT NULL` |
| `Json` | `text DEFAULT NULL` |
| `Choice` | `varchar(64) DEFAULT NULL` |

---

## 3. Core Services

### 3.1. Directory Structure

```
src/Core/ExtraProperty/
├── ARCHITECTURE.md
├── ExtraPropertyType.php
├── ExtraPropertyScope.php
├── ExtraPropertyDefinition.php
├── ExtraPropertyDefinitionCollection.php
├── ExtraPropertiesLazyArray.php
├── ExtraPropertyOptions.php
├── Registry/
│   ├── ExtraPropertyRegistryInterface.php
│   ├── ExtraPropertyRegistry.php
│   └── CachedExtraPropertyRegistry.php
├── Schema/
│   ├── ExtraPropertySchemaManagerInterface.php
│   ├── ExtraPropertySchemaManager.php
│   ├── CacheInvalidatingSchemaManager.php
│   └── ColumnDefinitionMapper.php
├── Storage/
│   ├── ExtraPropertyReaderInterface.php
│   ├── ExtraPropertyReader.php
│   ├── ExtraPropertyWriterInterface.php
│   └── ExtraPropertyWriter.php
├── Repository/
│   └── ExtraPropertyDefinitionRepository.php
├── Grid/
│   └── ExtraPropertyGridHelper.php
├── Form/
│   └── ExtraPropertyFormHelper.php
└── Exception/
    ├── ExtraPropertyException.php
    ├── ExtraPropertyNotFoundException.php
    ├── ExtraPropertyConflictException.php
    ├── InvalidExtraPropertyTypeException.php
    └── ExtraPropertySchemaException.php
```

### 3.2. ExtraPropertyType

PHP enum for supported field types:

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

enum ExtraPropertyType: int
{
    case Int = 1;
    case Bool = 2;
    case String = 3;
    case Float = 4;
    case Date = 5;
    case Html = 6;
    case Json = 7;
    case Choice = 8;
}
```

### 3.3. ExtraPropertyScope

PHP enum for property scope:

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

enum ExtraPropertyScope: int
{
    case Common = 1;
    case Lang = 2;
    case Shop = 3;
}
```

### 3.4. ExtraPropertyDefinition

Value object holding all metadata for a single extra property. Combines the required identification fields with the optional configuration from `ExtraPropertyOptions`.

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

class ExtraPropertyDefinition
{
    public function __construct(
        private readonly string $entityName,
        private readonly string $moduleName,
        private readonly string $fieldName,
        private readonly ExtraPropertyType $type,
        private readonly ExtraPropertyScope $scope = ExtraPropertyScope::Common,
        private readonly ExtraPropertyOptions $options = new ExtraPropertyOptions(),
    ) {}

    public function getColumnName(): string
    {
        return $this->moduleName . '_' . $this->fieldName;
    }

    public function getOptions(): ExtraPropertyOptions
    {
        return $this->options;
    }

    // ... getters for entityName, moduleName, fieldName, type, scope
}
```

### 3.5. ExtraPropertyOptions

DTO for the optional configuration passed to `registerExtraProperty()`. Provides a clear contract with IDE autocompletion.

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

class ExtraPropertyOptions
{
    public function __construct(
        public readonly bool $required = false,
        public readonly ?string $defaultValue = null,
        public readonly ?int $size = null,
        public readonly ?string $validate = null,
        public readonly ?array $choices = null,
        public readonly bool $apiVisible = true,
        public readonly ?string $apiMapping = null,
        public readonly ?string $formPosition = null,
        public readonly ?string $formType = null,
        public readonly ?array $formOptions = null,
    ) {}
}
```

Usage:
```php
$this->registerExtraProperty(
    'product',
    'custom_size',
    ExtraPropertyType::String,
    ExtraPropertyScope::Common,
    new ExtraPropertyOptions(size: 64, validate: 'isGenericName'),
);
```

### 3.6. ExtraPropertyRegistry

The registry loads all registered definitions from `ps_extra_property_definition`. The interface focuses purely on reading definitions; caching is handled via decoration.

**Interface**: `ExtraPropertyRegistryInterface`

Key methods:
- `getByEntity(string $entityName): ExtraPropertyDefinitionCollection` — get all definitions for an entity
- `getByModule(string $moduleName): ExtraPropertyDefinitionCollection` — get all definitions for a module
- `get(string $entityName, string $moduleName, string $fieldName, ExtraPropertyScope $scope): ?ExtraPropertyDefinition` — get a specific definition
- `getAll(): ExtraPropertyDefinitionCollection`
- `hasExtraProperties(string $entityName): bool` — fast check used for lazy loading optimization

**Implementation**: `ExtraPropertyRegistry` implements the interface by querying `ExtraPropertyDefinitionRepository` directly.

**Cache decorator**: `CachedExtraPropertyRegistry` decorates `ExtraPropertyRegistryInterface`, using Symfony's `cache.app` pool to cache results. It exposes an additional `invalidateCache(): void` method (not part of the interface) for cache invalidation.

### 3.6. ExtraPropertySchemaManager

Manages the dynamic creation/modification of `_extra`, `_extra_lang`, and `_extra_shop` tables. Uses `Doctrine\DBAL\Connection`.

**Interface**: `ExtraPropertySchemaManagerInterface`

Key methods:
- `ensureTableExists(string $entityName, int $scope): void` — creates the table if it doesn't exist
- `addColumn(ExtraPropertyDefinition $definition): void` — adds a column to the appropriate table
- `removeColumn(ExtraPropertyDefinition $definition): void` — drops a column
- `dropTableIfEmpty(string $entityName, int $scope): void` — drops the table if no dynamic columns remain

**Cache-invalidating decorator**: `CacheInvalidatingSchemaManager` decorates `ExtraPropertySchemaManagerInterface`. After any schema mutation (`addColumn`, `removeColumn`, `dropTableIfEmpty`), it calls `CachedExtraPropertyRegistry::invalidateCache()` to ensure the registry cache stays in sync with the DB schema. This keeps cache invalidation decoupled from the registry interface itself.

### 3.7. ColumnDefinitionMapper

Maps `ExtraPropertyType` constants to SQL column definitions. Used by `ExtraPropertySchemaManager`.

### 3.8. ExtraPropertyReader

Reads extra property values from `_extra` tables. Uses `Doctrine\DBAL\Connection`.

**Interface**: `ExtraPropertyReaderInterface`

Key methods:
- `getExtraProperties(string $entityName, int $entityId, ?int $langId = null, ?int $shopId = null): array` — returns `['column_name' => value, ...]`
- `getExtraPropertiesForIds(string $entityName, array $entityIds, ?int $langId, ?int $shopId): array` — bulk read for lists/grids, returns `[entityId => ['column_name' => value, ...], ...]`

**Performance**: checks the registry first; if no extra properties exist for the entity, returns empty array immediately without DB query.

### 3.9. ExtraPropertyWriter

Writes extra property values to `_extra` tables. Uses `INSERT ... ON DUPLICATE KEY UPDATE` for upsert behavior.

**Interface**: `ExtraPropertyWriterInterface`

Key methods:
- `saveExtraProperties(string $entityName, int $entityId, array $values, ?int $langId = null, ?int $shopId = null): void`
- `deleteExtraProperties(string $entityName, int $entityId): void` — deletes rows from all three extra tables

### 3.10. ExtraPropertyDefinitionRepository

CRUD operations for the `ps_extra_property_definition` table. Uses `Doctrine\DBAL\Connection`.

Key methods:
- `save(ExtraPropertyDefinition $definition): int` — INSERT, returns generated ID
- `delete(string $entityName, string $moduleName, string $fieldName): void`
- `deleteByModule(string $moduleName): void` — for module uninstall cleanup
- `findAll(): ExtraPropertyDefinitionCollection`
- `findByEntity(string $entityName): ExtraPropertyDefinitionCollection`
- `findByModule(string $moduleName): ExtraPropertyDefinitionCollection`

### 3.11. Service Configuration

Located at `src/PrestaShopBundle/Resources/config/services/core/extra_property.yml`:

```yaml
services:
  _defaults:
    public: false

  PrestaShop\PrestaShop\Core\ExtraProperty\Repository\ExtraPropertyDefinitionRepository:
    arguments:
      $connection: '@doctrine.dbal.default_connection'
      $dbPrefix: '%database_prefix%'

  # Registry: base implementation + cache decorator
  PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistry:
    arguments:
      - '@PrestaShop\PrestaShop\Core\ExtraProperty\Repository\ExtraPropertyDefinitionRepository'

  PrestaShop\PrestaShop\Core\ExtraProperty\Registry\CachedExtraPropertyRegistry:
    arguments:
      - '@PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistry'
      - '@cache.app'

  PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistryInterface:
    '@PrestaShop\PrestaShop\Core\ExtraProperty\Registry\CachedExtraPropertyRegistry'

  # Schema manager: base implementation + cache-invalidating decorator
  PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ExtraPropertySchemaManager:
    arguments:
      $connection: '@doctrine.dbal.default_connection'
      $dbPrefix: '%database_prefix%'
      $columnMapper: '@PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ColumnDefinitionMapper'

  PrestaShop\PrestaShop\Core\ExtraProperty\Schema\CacheInvalidatingSchemaManager:
    arguments:
      - '@PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ExtraPropertySchemaManager'
      - '@PrestaShop\PrestaShop\Core\ExtraProperty\Registry\CachedExtraPropertyRegistry'

  PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ExtraPropertySchemaManagerInterface:
    '@PrestaShop\PrestaShop\Core\ExtraProperty\Schema\CacheInvalidatingSchemaManager'

  PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ColumnDefinitionMapper: ~

  PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyReaderInterface:
    '@PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyReader'

  PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyReader:
    arguments:
      $connection: '@doctrine.dbal.default_connection'
      $dbPrefix: '%database_prefix%'
      $registry: '@PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistryInterface'

  PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyWriterInterface:
    '@PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyWriter'

  PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyWriter:
    arguments:
      $connection: '@doctrine.dbal.default_connection'
      $dbPrefix: '%database_prefix%'
      $registry: '@PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistryInterface'

  prestashop.core.extra_property.grid.helper:
    class: PrestaShop\PrestaShop\Core\ExtraProperty\Grid\ExtraPropertyGridHelper
    public: true
    arguments:
      $registry: '@PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistryInterface'
      $dbPrefix: '%database_prefix%'

  prestashop.core.extra_property.form.helper:
    class: PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormHelper
    public: true
    arguments:
      $registry: '@PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistryInterface'
      $reader: '@PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyReaderInterface'
      $writer: '@PrestaShop\PrestaShop\Core\ExtraProperty\Storage\ExtraPropertyWriterInterface'
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
├── ValueObject/
│   └── ExtraPropertyId.php
└── Exception/
    └── ExtraPropertyDomainException.php
```

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

**`UpdateExtraPropertyValuesCommand`**: Takes entity name, entity ID, associative array of `column_name => value`, optional lang ID and shop ID. Used by API write processors and BO form handlers.

**`GetExtraPropertyDefinitions`**: Query to list definitions, filterable by entity name and/or module name. Used by the Admin API to list available extra properties.

**`GetExtraPropertyValues`**: Query to read values for a specific entity instance. Used by the Admin API to include extra properties in entity responses.

---

## 5. Module Integration

### 5.1. New Methods on Module Class

File: `classes/module/Module.php`

#### `registerExtraProperty()`

```php
/**
 * Register an extra property for an entity.
 *
 * @param string $entityName Entity table name (e.g., 'product', 'customer')
 * @param string $fieldName Field name (will be prefixed with module name)
 * @param ExtraPropertyType $type
 * @param ExtraPropertyScope $scope
 * @param ExtraPropertyOptions|null $options Optional configuration DTO
 *
 * @return bool
 */
public function registerExtraProperty(
    string $entityName,
    string $fieldName,
    ExtraPropertyType $type,
    ExtraPropertyScope $scope = ExtraPropertyScope::Common,
    ?ExtraPropertyOptions $options = null,
): bool
```

This method calls core services directly (no CommandBus):
1. Validates the module is installed (`$this->id` must be set)
2. Validates column name length: `strlen($this->name . '_' . $fieldName) <= 64`
3. Creates an `ExtraPropertyDefinition` value object
4. Calls `ExtraPropertyDefinitionRepository::save()` to persist
5. Calls `ExtraPropertySchemaManager::ensureTableExists()` + `addColumn()` (cache invalidation is handled by the `CacheInvalidatingSchemaManager` decorator)

#### `unregisterExtraProperty()`

```php
/**
 * Unregister an extra property for an entity.
 *
 * @param string $entityName Entity table name
 * @param string $fieldName Field name (without module prefix)
 *
 * @return bool
 */
public function unregisterExtraProperty(string $entityName, string $fieldName): bool
```

This method:
1. Calls `ExtraPropertySchemaManager::removeColumn()` (cache invalidation handled by decorator)
2. Calls `ExtraPropertySchemaManager::dropTableIfEmpty()`
3. Calls `ExtraPropertyDefinitionRepository::delete()`

#### `unregisterAllExtraProperties()`

Called internally during `uninstall()`. Queries the registry for all definitions belonging to `$this->name` and unregisters each one.

### 5.2. Automatic Cleanup on Uninstall

In `Module::uninstall()`, before existing cleanup logic:

```php
$this->unregisterAllExtraProperties();
```

### 5.3. Module Usage Example

```php
class MyModule extends Module
{
    public function install()
    {
        return parent::install()
            && $this->registerExtraProperty(
                'product',
                'custom_size',
                ExtraPropertyType::String,
                ExtraPropertyScope::Common,
                new ExtraPropertyOptions(size: 64, validate: 'isGenericName'),
            )
            && $this->registerExtraProperty(
                'product',
                'custom_label',
                ExtraPropertyType::String,
                ExtraPropertyScope::Lang,
                new ExtraPropertyOptions(size: 255),
            )
            && $this->registerExtraProperty(
                'product',
                'shop_specific_flag',
                ExtraPropertyType::Bool,
                ExtraPropertyScope::Shop,
            )
            && $this->registerHook('actionProductFormBuilderModifier');
    }

    public function uninstall()
    {
        // Extra properties auto-cleaned by parent::uninstall()
        return parent::uninstall();
    }
}
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

A dedicated `ExtraPropertiesLazyArray` class (in `src/Core/ExtraProperty/`) extends `AbstractLazyArray`. It wraps extra property access so that values are loaded from DB only on first access via `ArrayAccess` (`$object->extra_properties['my_field']`).

```php
namespace PrestaShop\PrestaShop\Core\ExtraProperty;

use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;

class ExtraPropertiesLazyArray extends AbstractLazyArray
{
    private bool $loaded = false;

    public function __construct(
        private readonly string $entityName,
        private readonly int $entityId,
        private readonly ?int $langId,
        private readonly ?int $shopId,
    ) {}

    public function offsetGet(mixed $offset): mixed
    {
        $this->loadIfNeeded();
        return parent::offsetGet($offset);
    }

    public function offsetExists(mixed $offset): bool
    {
        $this->loadIfNeeded();
        return parent::offsetExists($offset);
    }

    private function loadIfNeeded(): void
    {
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;

        $reader = ServiceLocator::get(ExtraPropertyReaderInterface::class);
        $values = $reader->getExtraProperties(
            $this->entityName,
            $this->entityId,
            $this->langId,
            $this->shopId
        );
        $this->appendArray($values);
    }

    // ... methods to set values in memory for persistence
}
```

### 6.2. ObjectModel Integration

File: `classes/ObjectModel.php`

```php
/** @var ExtraPropertiesLazyArray Extra properties with lazy-loading from _extra tables */
public $extra_properties;
```

The `extra_properties` field is initialized in the ObjectModel constructor (after entity data is loaded) as an `ExtraPropertiesLazyArray` instance. This allows transparent access:

```php
// Accessing a value triggers lazy-loading automatically
$value = $product->extra_properties['mymodule_custom_size'];

// Iteration also triggers loading
foreach ($product->extra_properties as $key => $value) { ... }

// JSON serialization loads everything
json_encode($product->extra_properties);
```

Additional convenience methods on ObjectModel:

```php
/**
 * Get all extra properties (triggers lazy-load).
 *
 * @return array Associative array of column_name => value
 */
public function getExtraProperties(): array

/**
 * Get a single extra property value.
 *
 * @param string $columnName The full column name (e.g., 'mymodule_custom_size')
 *
 * @return mixed|null
 */
public function getExtraProperty(string $columnName): mixed

/**
 * Set extra property values in memory. Persisted on add()/update().
 *
 * @param array $values Associative array of column_name => value
 */
public function setExtraProperties(array $values): void
```

### 6.3. Lazy Loading

Extra properties are NOT loaded in the ObjectModel constructor. The `ExtraPropertiesLazyArray` only queries the DB on first access (e.g., `$object->extra_properties['field']`, iteration, or `json_encode`). If no extra properties are registered for the entity (checked via `ExtraPropertyRegistry::hasExtraProperties()`), an empty array is returned immediately without any DB query.

### 6.4. Automatic Persistence

In the `add()` method, after the `actionObject*AddAfter` hook, the `ExtraPropertiesLazyArray` is checked for any values that have been set in memory and persists them:

```php
if ($this->extra_properties->hasModifiedValues()) {
    $writer = ServiceLocator::get(ExtraPropertyWriterInterface::class);
    $writer->saveExtraProperties(
        $this->def['table'],
        (int) $this->id,
        $this->extra_properties->getModifiedValues(),
        $this->id_lang,
        $this->id_shop
    );
}
```

Same pattern in `update()`.

In `delete()`:
```php
$writer = ServiceLocator::get(ExtraPropertyWriterInterface::class);
$writer->deleteExtraProperties($this->def['table'], (int) $this->id);
```

### 6.5. Front-Office Template Access

Since `extra_properties` is a public `ExtraPropertiesLazyArray` on any ObjectModel, it is automatically available in FO templates without any module-specific hook:

**In Smarty templates** (via presenter):
```smarty
{$product.extra_properties.mymodule_custom_size}
```

**In PHP** (any ObjectModel):
```php
$product->extra_properties['mymodule_custom_size'];
$customer->extra_properties['mymodule_vip_level'];
```

Modules can also use the `actionPresentProduct` hook to flatten extra properties into the root presenter array if needed:

```php
public function hookActionPresentProduct(array $params)
{
    $presentedProduct = $params['presentedProduct'];
    $presentedProduct->appendArray(
        $params['product']->getExtraProperties()
    );
}
```

### 6.5. Fixtures in Example Module

Since BO editing is not available in Phase 3, the example module should automatically insert fixture data for testing:

```php
public function install()
{
    // ... register extra properties ...

    // Insert fixtures for testing
    $writer = $this->get(ExtraPropertyWriterInterface::class);
    $writer->saveExtraProperties('product', 1, [
        'mymodule_custom_size' => 'XL',
    ]);

    return true;
}
```

---

## 7. Admin API Integration

### 7.1. Strategy

Extra properties are exposed as an `extraProperties` sub-object in entity API responses. This clearly distinguishes native fields from extra ones and avoids naming conflicts.

Example API response:

```json
{
  "productId": 1,
  "name": "T-shirt",
  "price": "19.99",
  "extraProperties": {
    "mymodule_custom_size": "XL",
    "mymodule_custom_label": "Limited Edition",
    "othermodule_is_organic": true
  }
}
```

### 7.2. Read Operations

A custom API Platform normalizer (or provider decorator) intercepts entity responses and appends extra property values.

**Visibility control**: Only properties with `api_visible = true` in the definition are included by default.

**GET parameter filtering**: API consumers can request specific fields:
```
GET /api/products/1?extraProperties[]=mymodule_custom_size&extraProperties[]=mymodule_custom_label
```

This is configurable per extra property definition — a property can be set to always appear or only when explicitly requested.

### 7.3. Write Operations

A processor decorator intercepts write requests, extracts the `extraProperties` sub-object, and dispatches `UpdateExtraPropertyValuesCommand`:

```
PATCH /api/products/1
{
  "extraProperties": {
    "mymodule_custom_size": "M"
  }
}
```

### 7.4. API Resource Mapping

The definition table allows specifying a custom API field name via mapping. For example, `mymodule_custom_size` can be exposed as `customSize` in the API:

```php
$this->registerExtraProperty('product', 'custom_size', ExtraPropertyType::String, ExtraPropertyScope::Common,
    new ExtraPropertyOptions(apiMapping: 'customSize'),
);
```

If no mapping is provided, the column name is used as-is.

### 7.5. Dedicated Management Endpoints

For listing and managing extra property definitions:

```
GET    /api/extra-property-definitions                  # List all definitions
GET    /api/extra-property-definitions/{entity}          # List by entity
```

These use the CQRS `GetExtraPropertyDefinitions` query.

---

## 8. Back-Office Form Integration

### 8.1. Strategy

Extra property fields are added to BO entity forms via the existing hook system (`action{FormName}FormBuilderModifier`). A helper service (`ExtraPropertyFormHelper`) simplifies adding the correct Symfony form types.

### 8.2. ExtraPropertyFormHelper

Located at `src/Core/ExtraProperty/Form/ExtraPropertyFormHelper.php`.

Key methods:

```php
/**
 * Add extra property fields to a form builder.
 * Automatically maps ExtraPropertyType to Symfony form types.
 */
public function addToFormBuilder(
    FormBuilderInterface $formBuilder,
    string $entityName,
    ?string $moduleName = null,  // null = all modules
    ?string $afterField = null   // position control
): void;

/**
 * Get form data for extra properties (for DataProvider hooks).
 */
public function getFormData(
    string $entityName,
    int $entityId,
    ?int $langId,
    ?int $shopId
): array;

/**
 * Save form data for extra properties (for form handler hooks).
 */
public function handleFormData(
    string $entityName,
    int $entityId,
    array $formData,
    ?int $langId,
    ?int $shopId
): void;
```

### 8.3. Type Mapping (ExtraPropertyType → Symfony FormType)

| ExtraPropertyType | Symfony Form Type | Notes |
|---|---|---|
| `Int` | `IntegerType` | |
| `Bool` | `SwitchType` | PrestaShop's custom switch form type |
| `String` | `TextType` | |
| `Float` | `NumberType` | `scale` option set from definition |
| `Date` | `DateTimePickerType` | PrestaShop's custom date picker |
| `Html` | `FormattedTextareaType` | PrestaShop's TinyMCE textarea |
| `Json` | `TextareaType` | |
| `Choice` | `ChoiceType` | `choices` from definition |

For `Lang` fields, the form type is wrapped in `TranslatableType`.

### 8.4. Basic Integration (via form_rest)

Fields added via `FormBuilderModifier` hooks are automatically rendered by `form_rest()` or `form_end()` calls in Twig templates. This means extra properties appear at the end of the form by default.

### 8.5. Module Usage in Form Hooks

```php
public function hookActionProductFormBuilderModifier(array $params)
{
    $formHelper = $this->get('prestashop.core.extra_property.form.helper');
    $formHelper->addToFormBuilder($params['form_builder'], 'product', $this->name);

    if (isset($params['id'])) {
        $data = $formHelper->getFormData('product', $params['id'], $langId, $shopId);
        $params['data'] = array_merge($params['data'], $data);
    }
}

public function hookActionAfterUpdateProductFormHandler(array $params)
{
    $formHelper = $this->get('prestashop.core.extra_property.form.helper');
    $formHelper->handleFormData(
        'product',
        $params['id'],
        $params['form_data'],
        $langId,
        $shopId
    );
}
```

### 8.6. Advanced Positioning (Phase 8)

For placing fields at specific positions rather than at the end, the definition can include form display metadata:

```php
$this->registerExtraProperty('product', 'custom_size', ExtraPropertyType::String, ExtraPropertyScope::Common,
    new ExtraPropertyOptions(
        formPosition: 'details.references',
        formType: CustomSizeType::class,
    ),
);
```

The `ExtraPropertyFormHelper` uses `FormBuilderModifier::addAfter()` to insert the field at the specified position.

---

## 9. Grid Integration

### 9.1. Strategy

Modules add extra property columns to BO grids via the existing hook system. A helper service simplifies the process.

### 9.2. ExtraPropertyGridHelper

Located at `src/Core/ExtraProperty/Grid/ExtraPropertyGridHelper.php`.

Key methods:

```php
/**
 * Add extra property columns and filters to a grid definition.
 */
public function addToGridDefinition(
    GridDefinition $definition,
    string $entityName,
    ?string $moduleName = null,  // null = all modules
    ?int $position = null        // column position
): void;

/**
 * Add LEFT JOINs for extra property tables to query builders.
 * Handles sorting and filtering.
 */
public function addToQueryBuilder(
    QueryBuilder $searchQueryBuilder,
    QueryBuilder $countQueryBuilder,
    string $entityName,
    string $entityAlias,     // e.g., 'p' for product
    string $primaryKey,      // e.g., 'id_product'
    SearchCriteriaInterface $searchCriteria
): void;
```

### 9.3. Query Builder Modification

The grid helper adds LEFT JOINs to the existing search query:

```sql
SELECT p.*, pe.mymodule_custom_size
FROM ps_product p
LEFT JOIN ps_product_extra pe ON pe.id_product = p.id_product
WHERE ...
ORDER BY pe.mymodule_custom_size ASC
```

This approach:
- Fetches extra properties in the same query (no N+1)
- Supports sorting via `ORDER BY`
- Supports filtering via `WHERE` clauses
- Does not break pagination

### 9.4. Column Type Mapping

| ExtraPropertyType | Grid Column Type |
|---|---|
| `Int`, `Float` | `DataColumn` |
| `Bool` | `ToggleColumn` |
| `String`, `Html` | `DataColumn` |
| `Date` | `DateTimeColumn` |
| `Choice` | `DataColumn` |
| `Json` | Not displayed in grid |

### 9.5. Module Usage in Grid Hooks

```php
public function hookActionProductGridDefinitionModifier(array $params)
{
    $gridHelper = $this->get('prestashop.core.extra_property.grid.helper');
    $gridHelper->addToGridDefinition(
        $params['definition'],
        'product',
        $this->name,
        5  // position after 5th column
    );
}

public function hookActionProductGridQueryBuilderModifier(array $params)
{
    $gridHelper = $this->get('prestashop.core.extra_property.grid.helper');
    $gridHelper->addToQueryBuilder(
        $params['search_query_builder'],
        $params['count_query_builder'],
        'product',
        'p',
        'id_product',
        $params['search_criteria']
    );
}
```

---

## 10. Supported Types

### Phase 2 (Initial types)

| Type | Constant | ObjectModel equiv. | Description |
|------|----------|---------------------|-------------|
| Boolean | `Bool` | `ObjectModel::TYPE_BOOL` | true/false, stored as `tinyint(1)` |
| Integer | `Int` | `ObjectModel::TYPE_INT` | Whole numbers |
| String | `String` | `ObjectModel::TYPE_STRING` | Text up to `size` characters |

### Phase 7 (Additional types)

| Type | Constant | Description |
|------|----------|-------------|
| Float | `Float` | Decimal numbers, stored as `decimal(20,6)` |
| DateTime | `Date` | Date and time values |
| Choice | `Choice` | Enum-like, configured with `choices` array (similar to Symfony `ChoiceType`) |
| JSON | `Json` | Arbitrary JSON data, auto `json_encode`/`json_decode` on read/write |

### HTML type

| Type | Constant | Description |
|------|----------|-------------|
| HTML | `Html` | Rich text content, purified via `Tools::purifyHTML()` |

---

## 11. Performance Considerations

1. **Registry caching**: The `ExtraPropertyRegistry` uses Symfony's `cache.app` pool. Cache is invalidated only when definitions change (register/unregister).

2. **Lazy loading in ObjectModel**: Extra properties are NOT loaded in the constructor. They are loaded on first `getExtraProperties()` / `getExtraProperty()` call. Entities that never access extra properties incur zero overhead.

3. **No-op when unused**: The reader checks `ExtraPropertyRegistry::hasExtraProperties()` first. If no extra properties exist for an entity, an empty array is returned immediately without DB query.

4. **Bulk reading in grids**: The `ExtraPropertyGridHelper` adds LEFT JOINs to existing grid queries, meaning extra properties are fetched alongside main entity data in a single query — no N+1 problem.

5. **Column-based storage**: Unlike WordPress-style meta tables (one row per meta value), extra properties are stored as columns. This enables:
   - SQL indexing and constraints
   - No row multiplication on large datasets
   - Reduced JOINs
   - Strong typing at database level

6. **Optional column indexing**: For extra properties used as grid filters, the schema manager can optionally add an index:
   ```sql
   ALTER TABLE ... ADD INDEX idx_{column_name} ({column_name});
   ```

---

## 12. Conflict Handling

1. **Column name uniqueness**: The column name `{module_name}_{field_name}` is enforced unique per entity via the DB unique key `entity_module_field`. Two different modules cannot collide because their module names are different.

2. **Module name uniqueness**: Module names are guaranteed unique in the `ps_module` table.

3. **Column name length**: Enforced to be <= 64 characters. `registerExtraProperty()` throws `ExtraPropertyConflictException` if exceeded.

4. **MariaDB limits**: ~1000 columns per table (65 KB row size limit). In practice, this allows hundreds of extra properties per entity.

5. **Type changes**: A module must unregister and re-register to change a field's type. `registerExtraProperty()` checks for existing definitions and throws `ExtraPropertyConflictException` if one already exists with different parameters.

---

## 13. Backward Compatibility

1. **No core table modifications**: All extra properties are stored in separate `_extra` tables.

2. **ObjectModel is extended, not broken**: New methods (`getExtraProperties()`, `setExtraProperties()`) are additive. Existing code is completely unaffected.

3. **Module opt-in**: Extra properties only exist when a module registers them. Zero overhead for shops without modules using this feature.

4. **API opt-in**: The `extraProperties` field in API responses is `null` by default and only populated when relevant.

5. **Standard hook integration**: Grid and form integration uses existing, stable hook mechanisms.

---

## 14. Phased Implementation Plan

### Phase 1 — POC: Validate DB Structure

**Goal**: Prove the dynamic table/column approach works at scale.

**Deliverables**:
- `ExtraPropertyType`, `ExtraPropertyScope` constants
- `ExtraPropertyDefinition` value object
- `ExtraPropertyDefinitionCollection`
- `ColumnDefinitionMapper`
- `ExtraPropertySchemaManager` (create/alter/drop tables)
- `ExtraPropertyDefinitionRepository` (CRUD for definitions)
- All exception classes
- `ps_extra_property_definition` table in `install-dev/data/db_structure.sql`
- Integration tests: register fields, verify tables/columns, insert test data, query performance

**Files**:
- `src/Core/ExtraProperty/ExtraPropertyType.php`
- `src/Core/ExtraProperty/ExtraPropertyScope.php`
- `src/Core/ExtraProperty/ExtraPropertyDefinition.php`
- `src/Core/ExtraProperty/ExtraPropertyDefinitionCollection.php`
- `src/Core/ExtraProperty/Schema/ColumnDefinitionMapper.php`
- `src/Core/ExtraProperty/Schema/ExtraPropertySchemaManagerInterface.php`
- `src/Core/ExtraProperty/Schema/ExtraPropertySchemaManager.php`
- `src/Core/ExtraProperty/Repository/ExtraPropertyDefinitionRepository.php`
- `src/Core/ExtraProperty/Exception/*.php`
- `install-dev/data/db_structure.sql` (modified)

---

### Phase 2 — Module Methods + Example Module

**Goal**: Modules can register/unregister extra properties. Two example modules demonstrate conflict-free coexistence.

**Deliverables**:
- `ExtraPropertyRegistry` service (loads and caches definitions)
- `Module::registerExtraProperty()`, `Module::unregisterExtraProperty()`, `Module::unregisterAllExtraProperties()`
- Cleanup in `Module::uninstall()`
- Service configuration YAML
- **Example module A** (`ps_extraproperty_example_a`): registers `custom_size` (string), `is_custom` (bool), `custom_weight` (int) on `product`
- **Example module B** (`ps_extraproperty_example_b`): registers `is_organic` (bool), `organic_cert` (string) on `product`
- Unit tests for registry, integration tests for module install/uninstall

**Files**:
- `src/Core/ExtraProperty/Registry/ExtraPropertyRegistryInterface.php`
- `src/Core/ExtraProperty/Registry/ExtraPropertyRegistry.php`
- `src/PrestaShopBundle/Resources/config/services/core/extra_property.yml`
- `classes/module/Module.php` (modified)
- `modules/ps_extraproperty_example_a/` (new module)
- `modules/ps_extraproperty_example_b/` (new module)

---

### Phase 3 — FO Integration (ObjectModel)

**Goal**: Extra properties are readable/writable via ObjectModel and displayable in FO.

**Deliverables**:
- `ExtraPropertyReader` and `ExtraPropertyWriter` services
- `ObjectModel::getExtraProperties()`, `getExtraProperty()`, `setExtraProperties()`
- Auto-save in `ObjectModel::add()` and `update()`
- Auto-delete in `ObjectModel::delete()`
- FO display via hook (example module inserts fixture data and displays via `actionPresentProduct`)
- Unit tests for reader/writer, integration tests for ObjectModel lifecycle

**Files**:
- `src/Core/ExtraProperty/Storage/ExtraPropertyReaderInterface.php`
- `src/Core/ExtraProperty/Storage/ExtraPropertyReader.php`
- `src/Core/ExtraProperty/Storage/ExtraPropertyWriterInterface.php`
- `src/Core/ExtraProperty/Storage/ExtraPropertyWriter.php`
- `classes/ObjectModel.php` (modified)
- Example modules updated with fixtures and FO hooks

---

### Phase 4 — Admin API Integration

**Goal**: Extra properties are readable and writable via the Admin API.

**Deliverables**:
- CQRS commands/queries for value read/write and definition listing
- API Platform normalizer/decorator for `extraProperties` sub-object
- GET parameter filtering support
- Dedicated endpoints for listing definitions
- Integration tests for API read/write

**Files**:
- `src/Core/Domain/ExtraProperty/Command/UpdateExtraPropertyValuesCommand.php`
- `src/Core/Domain/ExtraProperty/Query/GetExtraPropertyDefinitions.php`
- `src/Core/Domain/ExtraProperty/Query/GetExtraPropertyValues.php`
- Adapter handlers in `src/Adapter/ExtraProperty/`
- API normalizer/decorator in `src/PrestaShopBundle/ApiPlatform/`
- Service configuration for CQRS handlers

---

### Phase 5 — BO Form Integration (Basic)

**Goal**: Extra properties appear in BO entity edit forms and are saved correctly.

**Deliverables**:
- `ExtraPropertyFormHelper` service
- Automatic type mapping (ExtraPropertyType → Symfony FormType)
- `TranslatableType` wrapping for `Lang` fields
- Fields rendered via `form_rest` at the end of forms
- Example modules updated with form hooks

**Files**:
- `src/Core/ExtraProperty/Form/ExtraPropertyFormHelper.php`
- Example modules updated

---

### Phase 6 — Grid Integration

**Goal**: Extra properties appear as sortable/filterable columns in BO list grids.

**Deliverables**:
- `ExtraPropertyGridHelper` service
- LEFT JOIN-based query modification
- Column position control
- Sorting and filtering support
- Example modules updated with grid hooks

**Files**:
- `src/Core/ExtraProperty/Grid/ExtraPropertyGridHelper.php`
- Example modules updated

---

### Phase 7 — Additional Types

**Goal**: Support float, datetime, choice/enum, JSON types.

**Deliverables**:
- Extended `ExtraPropertyType` enum with `Float`, `Date`, `Choice`, `Json` cases
- `ColumnDefinitionMapper` updated with new SQL mappings
- `ExtraPropertyFormHelper` updated with new form type mappings
- `ExtraPropertyGridHelper` updated with new column type mappings
- JSON auto-encode/decode in reader/writer
- Choice type validation against allowed values
- Example modules updated with all types

---

### Phase 8 — Advanced BO Integration

**Goal**: Custom form types and field positioning.

**Deliverables**:
- Support for custom Symfony form type class in definition
- `form_position` option for propertyPath-based placement
- Custom Twig form themes for extra property fields
- Extended definition options (`form_type`, `form_position`, `form_options`)

---

### Phase 9 — Native BO Module for No-Code Management

**Goal**: Administrators can create and manage extra properties from the BO without writing code.

**Deliverables**:
- Native module (`ps_extraproperty` or similar)
- Admin controller: list, create, edit, delete extra property definitions
- UI for configuring type, scope, validation, form display, API visibility
- Import/export of definitions
- Hooks integration for extensibility

---

## 15. Testing Strategy

### Unit Tests
- `ExtraPropertyDefinition` value object validation
- `ColumnDefinitionMapper` type-to-SQL mapping
- `ExtraPropertyRegistry` caching behavior
- `ExtraPropertyFormHelper` type-to-form mapping
- `ExtraPropertyGridHelper` column/filter generation

### Integration Tests
- Full lifecycle: register → create entity with extras → read → update → delete → unregister
- Multi-module coexistence on same entity
- Module uninstall cleanup
- Schema manager table/column creation and removal
- ObjectModel `add()`/`update()`/`delete()` with extra properties

### Functional Tests (via Behat or similar)
- Admin API CRUD with extra properties
- BO form display and submission
- Grid display, sorting, filtering
- FO display via presenter hooks
