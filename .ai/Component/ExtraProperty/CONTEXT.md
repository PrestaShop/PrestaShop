# ExtraProperty Component

> **Status:** Draft — feature is under active development (Epic [#41422](https://github.com/PrestaShop/PrestaShop/issues/41422)). This file describes the merged code; see *Status & roadmap* for what is not done yet.

## Purpose

Lets any module register **typed extra fields** on existing PrestaShop entities (Product, Order, Customer…) without altering core tables. A single `Module::registerExtraProperty(ExtraPropertyDefinition)` call provisions storage and surfaces the field in the front-office (Smarty lazy arrays), the migrated back-office (Symfony forms + grids) and the Admin API. Storage is **column-based** (one DB column per property), not EAV — so values are indexable and joinable.

## Layers

| Layer | Path |
|-------|------|
| Definition: registry VO, enums, repository, cache | `src/Core/ExtraProperty/Definition/` |
| Schema: dynamic DDL (table/column create/alter/drop) | `src/Core/ExtraProperty/Schema/` |
| Value: reader, writer, caster, lazy bags | `src/Core/ExtraProperty/Value/` |
| Validation | `src/Core/ExtraProperty/Validation/` |
| BO form integration | `src/Core/ExtraProperty/Form/` |
| BO grid integration | `src/Core/ExtraProperty/Grid/` |
| Admin API integration: response subscriber + grid-record collector | `src/PrestaShopBundle/EventListener/API/ExtraPropertyApiSubscriber.php`, `src/Core/ExtraProperty/Api/` |
| Admin API validation + OpenAPI | `src/PrestaShopBundle/ApiPlatform/Validator/ExtraPropertyCQRSApiValidator.php`, `src/PrestaShopBundle/ApiPlatform/OpenApi/Adapter/ExtraPropertiesSchemaAdapter.php` |
| BO definition management domain | `src/Core/Domain/ExtraProperty/` |
| BO definition management handlers | `src/Adapter/ExtraProperty/` |
| BO definition management UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/ExtraPropertyDefinitionController.php`, `src/PrestaShopBundle/Form/Admin/Configure/AdvancedParameters/ExtraPropertyDefinition/` |
| Legacy + FO hooks | `classes/module/Module.php`, `classes/ObjectModel.php`, `src/Adapter/Presenter/AbstractLazyArray.php` |
| Registry table DDL | `install-dev/data/db_structure.sql` (`ps_extra_property_definition`) |
| Service wiring | `…/config/services/extra_property/{common,backend}.yml`, `…/config/services/core/grid/grid_extra_properties.yml` |

## Database structure

**Central registry — `ps_extra_property_definition`** (created at install). One row per registered property. Real columns: `entity_name`, `module_name` (NULL = core field, no module owner), `property_name`, `type` ENUM(`int,bool,string,float,date,html,json,choice`), `scope` ENUM(`common,lang,shop`), `sql_index` ENUM(`none,key,unique`), `size`, `default_value`, `constraints` (PHP-serialized Symfony `Constraint[]`), `display_front`, `associated_grids`/`associated_forms`/`associated_apis` (JSON placement & API-targeting DSL), `form_field_type`, `required`, `form_options`, and `label_*`/`description_*` wording+domain pairs. Unique key: `(entity_name, module_name, property_name)`.

**Dynamic per-entity value tables**, created lazily by `ExtraPropertySchemaManager` on first registration for an entity, one per scope:

| Scope | Table | Same value across… |
|-------|-------|--------------------|
| `common` | `{entity}_extra` | all shops + langs |
| `lang` | `{entity}_extra_lang` | per language (per shop too) |
| `shop` | `{entity}_extra_shop` | per shop |

The non-obvious part: each extra table is **built by mirroring the primary key of the matching native base table** via Doctrine introspection (`createExtraTableFromBaseTable`), not from hardcoded columns. Base table per scope: `common`→`{entity}`, `lang`→`{entity}_lang`, `shop`→`{entity}_shop` (`ExtraPropertyDefinition::getBaseTableName()`). So `product_extra_lang`'s PK is exactly `product_lang`'s PK (`id_product,id_lang,id_shop`). This guarantees every grid LEFT JOIN matches **at most one row** (1:1), so no `GROUP BY` is ever needed.

Each property is one column added with `ALTER TABLE … ADD COLUMN`; storage column name = `{module}_{property}` (or `{property}` for core fields). SQL type comes from `ColumnDefinitionMapper`: INT(11), TINYINT(1) UNSIGNED (bool), VARCHAR(size|255), DECIMAL(20,6), DATETIME, TEXT (html), LONGTEXT (json), `ENUM(...)` for choice (VARCHAR(64) fallback if no values). On unregister the column is dropped, and the table itself is dropped once only PK columns remain.

## Module-facing usage

A module registers a property in `install()` and removes it in `uninstall()` (see the **`demoextrafield`** example module: <https://github.com/PrestaShop/example-modules/tree/master/demoextrafield>):

```php
$this->registerExtraProperty(new ExtraPropertyDefinition(
    entityName: 'product', propertyName: 'video_link',
    type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::LANG,
    labelWording: 'Video link', labelDomain: 'Modules.Demoextrafield.Admin',
    displayFront: true, associatedForms: ['product'], associatedApis: ['/products', '/products/{productId}'],
));
// uninstall():  $this->unregisterExtraProperty($definition, dropData: true);
```

**Read/write values** — grouped by module key (`'_core'` for core fields), persisted on `ObjectModel::save()`:

```php
$product->extra_properties['demoextrafield']['video_link'];                // read
$product->extra_properties['demoextrafield']['video_link'] = 'https://…';  // write (saved on update())
```

```smarty
{$product.extra_properties.demoextrafield.video_link|default:''}
```

The Smarty/array index key is `extra_properties` (snake_case), and only present on entities whose lazy array opted in (Product, Category, …).

**Field placement** is declared as string entries (parsed in the VO, see `getFormEntry`/`getGridEntry`):
- `associatedForms`: `"formId[:path[:before|after]]"` — no mode → field appended inside the path container; `:before`/`:after` → placed relative to the last path segment as an anchor (e.g. `"product:options.suppliers:before"`). Empty → a dedicated extra-properties form section.
- `associatedGrids`: `"gridId[:columnId[:before|after]]"` (e.g. `"product:reference:after"`). Empty → placed before the grid `actions` column.

**Rendering** (derived from the logical type, not the override):
- BO form field = `form_field_type` FQCN if set, else **`TextType`**; LANG scope wrapped in `TranslatableType`; the definition's `constraints` (real Symfony `Constraint` objects) are attached to the field as-is so the form runs them natively (multiple errors per field). Requiredness is opt-in — the module passes `NotBlank`; the `required` flag (`isRequired()`) only drives the HTML required attribute on the BO form **and** the field's entry in the Admin API (OpenAPI) `required` list — it never adds a server-side `NotBlank`. (There is no per-type form-type map — `TextType` is the default for every untyped field.)
- Grid column: BOOL → `ToggleColumn` (route `admin_common_extra_properties_toggle`, shop resolved server-side), DATE → `DateTimeColumn`, JSON → skipped, everything else → `DataColumn`.
- Form field name, grid column id, grid SELECT alias and the inline key in API list responses all share `getFieldName()` = `extra_{module}_{property}`. The scope is intentionally not part of it — a property is unique per module + name.

## Admin API integration

Module-declared extra properties are exposed and managed on the Admin API, wired **only in the Admin API kernel** (`app/config/admin-api/services.yml`). `PrestaShopBundle\EventListener\API\ExtraPropertyApiSubscriber` (on `kernel.response`) is the single API-Platform-coupled bridge; the Core reader/writer it drives stay framework-agnostic.

- **Targeting** — each definition declares `associatedApis`: URI templates with an optional `:METHOD` modifier (`/products`, `/products/{productId}:GET,PATCH`), matched against the operation's literal `uriTemplate` + method (`ExtraPropertyDefinitionCollection::filterByApi`). No class→entity inference, so a field never leaks onto a resource it does not target. Replaces the old `display_api` boolean.
- **Read (item)** — a nested `extraProperties` sub-object keyed by module then property: COMMON scalars, LANG objects keyed by **locale** (converted from id_lang via `LocalizedValueUpdater`), SHOP flattened for the shop context. Read through `ExtraPropertyReaderInterface::getExtraProperties`.
- **Read (list)** — each item is enriched **inline** at its root under `getFieldName()`, as the single context-locale value. Grid-associated properties are reused from `ExtraPropertyApiListRecordCollector` (the grid query already fetched them, no extra read); API-only (non-grid) properties — and every property of a non-grid, CQRS-paginated list — are read in one batched query via `getMultipleExtraProperties`.
- **Write** — `POST`/`PUT`/`PATCH` accept the same sub-object; the subscriber filters it to the operation's definitions (so unrelated keys can't be written), converts LANG locale→id_lang, and persists via `ExtraPropertyWriterInterface::writeAll` with the request's `ShopConstraint`.
- **Validation** — `ExtraPropertyCQRSApiValidator` decorates `CQRSApiValidator`, merging extra-property violations with the resource's into a single `422` (paths `extraProperties.<module>.<field>`).
- **OpenAPI** — `ExtraPropertiesSchemaAdapter` documents the sub-object on the read and write schemas.

## Non-obvious patterns

- **`nullable` and `enumValues` are NOT stored in the registry** — the live storage column schema is their source of truth. `ExtraPropertyDefinitionRepository::enrichRowsWithColumnMetadata()` runs `SHOW COLUMNS` and injects synthetic `nullable`/`enum_values` keys consumed by `ExtraPropertyDefinition::fromRow()`.
- **Two-tier service wiring.** `common.yml` (FO+BO): repository, cached read decorator, reader, writer, validator. `backend.yml` (BO-only): schema manager (DDL, needs logger), registry, form services — registration/DDL only happen back-office. The Admin API bridge is wired separately in `app/config/admin-api/services.yml` (Admin API kernel only). In FO the `WriterInterface` aliases the plain repository (no writes, no cache invalidation).
- **Constraint-based validation, available in every container.** `ExtraPropertyValidator` requires the Symfony `validator`. The three Symfony kernels have it; the **hand-built FO legacy container does not**, so it is wired there by `src/Adapter/Container/ValidatorBuilderExtension.php` — a `ContainerConstraintValidatorFactory` that resolves the PS validators whose deps exist in FO (`TypedRegex`, `CleanHtml`) and **gracefully skips + `error_log`s** the rest (`DefaultLanguage` needs `LanguageContext`, absent in FO). Per-language rules are declared with `Assert\All`; whole-array rules (e.g. `DefaultLanguage`) are bare. **Edge case:** an ObjectModel loaded with a `langId` exposes a LANG value as a *scalar* — `validateValue()` then applies only the unwrapped `All` rules and skips whole-array constraints. See [CONTAINERS.md](../../CONTAINERS.md) (esp. why this validator must compile in the FO container: it is built even during BO boot).
- **`CachedExtraPropertyDefinitionRepository` implements BOTH read and write interfaces** over one filesystem cache pool; every write invalidates. The registry itself is uncached and gets the cached repo injected as its writer.
- **The registry refuses destructive schema changes** on an already-registered property (type/scope change, STRING size decrease, NULL→NOT NULL, ENUM value removal) — those require unregister + re-register. Non-destructive drift (default change, size increase, nullable relax, enum addition) is reconciled onto the live column via `ALTER TABLE … MODIFY COLUMN`.
- **The Back Office registry UI manages only core-owned definitions** (`module_name IS NULL`). Module-owned definitions remain visible in the grid but edit/delete/toggle actions reject them with `ProtectedModuleExtraPropertyDefinitionException`, because modules are still the source of truth for their own registered fields. `ExtraPropertyDefinition::isModuleOwned()` is the single check.
- **Module-owned rows get a dedicated `viewAction()` instead of `editAction()`**, reached directly from the grid (no redirect in the normal flow): the "edit" row action is granted by `NonModuleExtraPropertyDefinitionAccessibilityChecker`, the "view" row action by its exact inverse, `ModuleExtraPropertyDefinitionAccessibilityChecker` — a row only ever gets one of the two. `viewAction()` reuses the same `FormBuilderInterface`/`ExtraPropertyDefinitionType` as create/edit purely for display (`getFormFor($id)`, no `FormHandlerInterface`, no `handleRequest()` — nothing is ever submitted there). Fields are not rendered `disabled` in this read-only view (a generic "disable everything dynamically" mechanism was deliberately deferred by the reviewer to a follow-up PR reworking form validation, to avoid threading a disable flag through the `CardType` sub-form-types now).
- **BO definition edits preserve only entity/property/type/scope.** Those four are read-only forever (changing them implies moving storage tables, only unregister+register can do that). `sql_index`, `size`, `nullable` and `enumValues` ARE editable from the BO edit form, but only non-destructively (size↑, nullable relaxed, enum values added, index strategy changed freely) — `ExtraPropertyRegistry::hasStorageChanges()` is what actually enforces the destructive/non-destructive boundary; the command/form layer does not duplicate that check, it just lets a destructive attempt fail server-side.
- **BO definition handlers go through the repository + VO, never raw SQL.** `ExtraPropertyDefinitionRepositoryInterface::getUnprotectedDefinitionById(int $id): ExtraPropertyDefinition` is the single lookup-by-id used by every write handler (Edit/Delete) — it centralizes both guards a write needs: it throws `ExtraPropertyDefinitionNotFoundException` when no row matches, and `ProtectedModuleExtraPropertyDefinitionException` when the definition is module-owned, so handlers no longer duplicate either check. The plain `getDefinitionById(int $id): ?ExtraPropertyDefinition` (nullable, no guard) remains for legitimate read-only access to a possibly-protected definition (`GetExtraPropertyDefinitionForEditingHandler`, the "view" page for module-owned rows). `ExtraPropertyDefinition::withOverrides(array $overrides): self` rebuilds a definition with a subset of fields replaced (keyed by constructor param name, `array_key_exists` so a field can legitimately be set back to `null`); handlers call `$registry->register($definition->withOverrides([...]))` to persist. `ExtraPropertyRegistryInterface::register()` returns `int|false` (the registry row id on success), not `bool` — callers get the id directly instead of an extra lookup query.
- **The BO registry grid has no toggle action.** `display_front` is shown as a read-only `BooleanColumn`; it is only editable through the create/edit form (`ExtraPropertyDefinitionVisibilityType`). An earlier iteration exposed it as an AJAX-toggleable `ToggleColumn`, generic enough to support several togglable fields — once only `display_front` remained, the dedicated Command/Handler/route/JS were removed as unnecessary indirection for a single field already reachable from the form.
- **Bulk delete reuses the single-delete handler.** `BulkDeleteExtraPropertyDefinitionHandler extends AbstractBulkCommandHandler` and delegates each id to `DeleteExtraPropertyDefinitionHandlerInterface` (no duplicated not-found/module-owned logic); per-item `ExtraPropertyException` failures (e.g. module-owned ids in the selection) are aggregated into `BulkExtraPropertyException` instead of stopping the batch.
- **The BO create/edit form is split into 5 `CardType` sub-form-types** (`ExtraPropertyDefinitionFieldDefinitionType`, `…VisibilityType`, `…LabelsType`, `…ValidationType`, `…AdvancedType`), each mapping to one card section. The root `ExtraPropertyDefinitionType` only aggregates them — it renders no field itself, so `Blocks/form.html.twig` is a single `form_row(extraPropertyDefinitionForm)` (no per-field Twig listing) and the submitted/loaded data is nested by section (`$data['field_definition']['entity_name']`, etc.) — see `ExtraPropertyDefinitionFormDataProvider`/`FormDataHandler`. `CardType` itself gained an optional `icon` option (Material icon name) rendered in the `card_row` Twig block.
- **Grouped value shape everywhere.** Reader returns / writer accepts `[moduleKey => [propertyName => value]]` (`'_core'` for core fields). For lang scope, `langId = null` yields `[id_lang => value]` arrays (BO/API edit-all-langs), an int yields a scalar (FO).
- **`ExtraPropertiesBag`** is the single lazy resolver for both `ObjectModel::$extra_properties` and FO lazy arrays; it dirty-tracks writes. ObjectModel persists modified values in `add()`/`update()` via `persistExtraProperties()`. FO arrays opt in via `AbstractLazyArray::initExtraPropertiesBag()` (Product, Category, Manufacturer, Supplier, Store, Order, OrderDetail, OrderReturn, Cart). FO vs BO is **auto-detected** inside `createForEntity()` via `Context::isFrontOfficeContext()` (fallback `_PS_FRONT_DIR_`): FO reads see only `display_front = 1` fields, BO/CLI/API see all — so native `$entity->extra_properties` access is FO-safe with no module code.
- **Identifier safety contract.** The `ExtraPropertyDefinition` constructor validates `entityName`, `propertyName` and the computed storage column as SQL identifiers (≤64 chars), so DDL consumers embed them in SQL without re-validating. `entityName` is normalized to snake_case (`tableize()`) at construction.

## Status & roadmap

- **Admin API integration is delivered** ([#41543](https://github.com/PrestaShop/PrestaShop/issues/41543)) — see *Admin API integration* above. Defining extra properties *through* the API (CRUD on the registry itself) is still open [#41542](https://github.com/PrestaShop/PrestaShop/issues/41542). The BO definition management CQRS layer added by this branch is separate from the Admin API bridge.
- **Module uninstall cleanup is not automatic** — modules must call `unregisterExtraProperty($definition, dropData: true)` themselves.
- Open sub-issues: advanced form placement via `property_path` [#41425], multishop handling [#41568], automated tests [#41541], validation hardening [#41544], docs [#41429], security review [#41640], translation scanner support [#41725], cart entity [#41424].

## Canonical examples

- `src/Core/ExtraProperty/Definition/ExtraPropertyDefinition.php` — immutable VO; registration config + read DTO + all naming/placement logic
- `src/Core/ExtraProperty/Schema/ExtraPropertySchemaManager.php` — dynamic table/column DDL (mirrors base-table PK)
- `src/Core/ExtraProperty/Value/ExtraPropertiesBag.php` — lazy grouped bag shared by ObjectModel + FO
- `src/Core/ExtraProperty/Schema/ColumnDefinitionMapper.php` — ExtraPropertyType → SQL column fragment
- `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/ExtraPropertyDefinitionController.php` — BO registry CRUD page
- `src/Core/Grid/Definition/Factory/ExtraPropertyDefinitionGridDefinitionFactory.php` — BO registry grid definition

## Related

- [MULTISTORE.md](../../MULTISTORE.md) — scope routing (`common`/`lang`/`shop`) resolves through `ShopConstraint`/`ShopContext`/`LanguageContext`
- [Grid Component](../Grid/CONTEXT.md) — extra columns are injected via grid definition/query-builder modifiers; the PK-mirroring 1:1 join invariant lets them skip `GROUP BY`
