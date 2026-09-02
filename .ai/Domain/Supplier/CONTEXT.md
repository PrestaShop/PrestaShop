# Supplier Domain

## Purpose

Manages product suppliers (name, address, logo, associated products) used in the catalog purchasing workflow. Does not handle purchase orders or inventory; supplier-order logic lives in a separate context.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Supplier/` — Commands, Queries, QueryResults, ValueObjects, Exceptions, `SupplierSettings.php` |
| Adapter | `src/Adapter/Supplier/` — CommandHandler, QueryHandler, Repository, plus `AbstractSupplierHandler.php`, `SupplierAddressProvider.php`, `SupplierLogoThumbnailProvider.php`, `SupplierOrderValidator.php`, `SupplierProductSearchProvider.php` |
| Legacy ObjectModel | `classes/Supplier.php` (481 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/SupplierController.php`, `src/Core/Grid/Definition/Factory/SupplierGridDefinitionFactory.php`, `src/PrestaShopBundle/Form/Admin/Sell/Supplier/SupplierType.php` |

## Non-obvious patterns

- `src/Adapter/Supplier/AbstractSupplierHandler.php` provides shared legacy-ObjectModel helpers (load/save/address lookup) reused across multiple command handlers — extend it rather than duplicating ObjectModel calls.
- `SupplierSettings.php` is a pure-constants class in the Core domain that enforces field-length constraints (name: 64, meta title: 255, meta description: 512); validate against it in commands, not in the handler.
- The adapter contains dedicated providers (`SupplierLogoThumbnailProvider`, `SupplierProductSearchProvider`) consumed outside the CQRS flow (e.g., by views and search components).

## Canonical examples

- `src/Core/Domain/Supplier/Command/AddSupplierCommand.php` + `src/Adapter/Supplier/CommandHandler/AddSupplierCommandHandler.php`
- `src/Core/Domain/Supplier/Query/GetSupplierForEditing.php` + `src/Adapter/Supplier/QueryHandler/`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Supplier/` — Behat behavior scenarios
