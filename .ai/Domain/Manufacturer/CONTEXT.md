# Manufacturer Domain

## Purpose

Manages brand/manufacturer records including logo images, addresses, and product associations. Does not manage Supplier entities (a separate domain) despite the similar data model.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Manufacturer/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Manufacturer/` — handler implementations, repository, `ManufacturerDataProvider.php`, `ManufacturerLogoThumbnailProvider.php`, `ManufacturerProductSearchProvider.php` |
| Legacy ObjectModel | `classes/Manufacturer.php` (617 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/ManufacturerController.php` |

## Non-obvious patterns

- `src/Adapter/Manufacturer/AbstractManufacturerHandler.php` provides shared logic for command handlers (logo handling, address management) — extend this when adding new write handlers.
- `ManufacturerProductSearchProvider.php` implements the product search autocomplete for the manufacturer form, wiring into the generic search component rather than a dedicated query.

## Canonical examples

- `src/Core/Domain/Manufacturer/Command/AddManufacturerCommand.php` + `src/Adapter/Manufacturer/CommandHandler/`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Manufacturer/` — Behat behavior scenarios
