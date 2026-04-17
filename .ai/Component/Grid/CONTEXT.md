# Grid Component

## Purpose

Infrastructure for rendering and managing back-office data tables: column definitions, filters, row/bulk actions, query builders, data factories, and drag-and-drop position reordering. Does not contain any business data — each domain provides its own `GridDefinitionFactory` and Doctrine query builder.

## Layers

| Layer | Path |
|-------|------|
| Core contracts + factory | `src/Core/Grid/` |
| Column types, row/bulk actions | `src/Core/Grid/Column/`, `src/Core/Grid/Action/` |
| Query builder base | `src/Core/Grid/Query/AbstractDoctrineQueryBuilder.php` |
| Position updater | `src/Core/Grid/Position/` |
| Adapter utilities | `src/Adapter/Grid/` |

## Non-obvious patterns

- `AbstractGridDefinitionFactory` dispatches `action{GridId}GridDefinitionModifier` hook — modules add columns/actions without touching core code
- `SearchCriteriaInterface` is stored as a Symfony request attribute per grid, not a service — each grid type has its own `{Domain}Filters` class present in `src/Core/Search/Filters`
- For specific use cases a dedicated filter builder may be needed — see `src/Core/Search/Builder/TypedBuilder`
- Position updater (`GridPositionUpdater`) lives inside the Grid source tree but can be used by any entity that supports manual ordering, independent of grid rendering
- 60+ concrete query builders exist (one per domain grid) — all extend `AbstractDoctrineQueryBuilder` and implement `getSearchQueryBuilder()` + `getCountQueryBuilder()`

## Canonical examples

- `src/Core/Grid/Definition/Factory/AbstractGridDefinitionFactory.php` — base class showing the pattern every grid definition must follow
- `src/Core/Grid/Definition/Factory/ProductGridDefinitionFactory.php` — concrete implementation
- `src/Core/Grid/Query/LanguageQueryBuilder.php` — simple concrete query builder

## Related

- [Forms Component](../Forms/CONTEXT.md) — filter forms use `FormChoiceProviderInterface`
- [Hook Component](../Hook/CONTEXT.md) — `action{GridId}GridDefinitionModifier` hook for module extensibility
- [PositionUpdater Component](../PositionUpdater/CONTEXT.md) — drag-and-drop reordering sub-layer
