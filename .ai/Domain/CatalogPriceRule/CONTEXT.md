# CatalogPriceRule Domain

## Purpose

Manages catalog price rules that apply automatic price reductions to products based on configurable conditions (currency, country, group, shop). Does not handle cart rules or specific prices applied to individual orders.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/CatalogPriceRule/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/CatalogPriceRule/` — handler implementations, repository |
| Legacy ObjectModel | `classes/SpecificPriceRule.php` (310 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/CatalogPriceRuleController.php`, `src/Core/Grid/Definition/Factory/CatalogPriceRuleGridDefinitionFactory.php` |

## Non-obvious patterns

- The legacy ObjectModel is `SpecificPriceRule` (not `CatalogPriceRule`); the domain uses the more descriptive name introduced with CQRS migration.
- A dedicated `CatalogPriceRuleGridDataFactory` exists alongside the standard grid definition factory, handling data transformation separately from grid structure.

## Canonical examples

- `src/Core/Domain/CatalogPriceRule/Command/AddCatalogPriceRuleCommand.php` + `src/Adapter/CatalogPriceRule/CommandHandler/AddCatalogPriceRuleHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/CatalogPriceRule/` — Behat behavior scenarios
