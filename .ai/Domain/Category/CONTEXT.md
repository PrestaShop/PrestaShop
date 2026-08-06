# Category Domain

## Purpose

Manages the product category tree including creation, editing, position updates, image handling, and SEO settings. Does not manage product assignment to categories (handled via the Product domain).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Category/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Category/` — handler implementations, repositories, data providers |
| Legacy ObjectModel | `classes/Category.php` (2459 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/CategoryController.php`, `src/Core/Grid/Definition/Factory/` (CategoryGridFactoryDecorator) |

## Non-obvious patterns

- The domain root contains two settings classes (`CategorySettings.php`, `SeoSettings.php`) for DB-constrained constants (max lengths, recommended lengths) that have no corresponding ValueObject.
- The grid layer uses a `CategoryGridFactoryDecorator` rather than a plain `GridDefinitionFactory`, allowing category-tree-specific display logic to be injected.
- `src/Adapter/Category/` includes multiple view/search data providers (`CategoryViewDataProvider`, `CategoryProductSearchProvider`) consumed outside the CQRS flow.
- The `EditRootCategoryCommand` exists separately from `EditCategoryCommand` because the root category has different validation rules.

## Canonical examples

- `src/Core/Domain/Category/Command/AddCategoryCommand.php` + `src/Adapter/Category/CommandHandler/` (AddCategoryHandler)

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Category/` — Behat behavior scenarios
