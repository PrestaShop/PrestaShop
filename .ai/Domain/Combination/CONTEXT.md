# Combination Domain

## Purpose

Manages product attribute combinations (variants): their creation, update, deletion, pricing, stock, images, and supplier assignments. Combinations are sub-entities of Product and have no independent lifecycle outside one.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Product/Combination/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Settings | `src/Core/Domain/Combination/CombinationSettings.php` — label length constants only |
| Adapter handlers | `src/Adapter/Product/Combination/CommandHandler/` and `QueryHandler/` |
| Adapter internals | `src/Adapter/Product/Combination/{Create,Update,Validate}/` — creator, fillers, updaters, validator |
| Repository | `src/Adapter/Product/Combination/Repository/CombinationRepository.php` |
| Legacy ObjectModel | `classes/Combination.php` (545 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/Product/CombinationController.php` |

## Non-obvious patterns

- CQRS namespace lives under `Product/Combination`, not a top-level `Combination` domain — `src/Core/Domain/Product/Combination/`.
- `UpdateCombinationHandler` delegates field mapping to a chain of `CombinationFiller` implementations (`DetailsFiller`, `PricesFiller`, `StockInformationFiller`) rather than mapping everything inline.
- `NoCombinationId` is a null-object ValueObject used where a combination is optional (e.g. pack items, supplier associations).
- CommandBuilders are nested under the **Product** CommandBuilder path, not a standalone Combination path: `src/Core/Form/IdentifiableObject/CommandBuilder/Product/Combination/`.

## Canonical examples

- `UpdateCombinationCommand` → `UpdateCombinationHandler` (uses filler chain + `CombinationRepository`)
- `GenerateProductCombinationsCommand` → `GenerateProductCombinationsHandler` + `CombinationCreator`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md) — 5 CombinationCommandBuilders in `src/Core/Form/IdentifiableObject/CommandBuilder/Product/Combination/`
- [Product Domain](../Product/CONTEXT.md) — Combinations are sub-entities of Product; `CombinationId` is referenced throughout the Product domain
- `tests/Integration/Behaviour/Features/Scenario/Product/Combination/` — Behat scenarios (single-shop and multi-shop)
