# CartRule Domain

## Purpose

Provides read access to cart rules (vouchers/cart-level discounts) via queries. Write operations (create, edit, delete) are handled by the legacy `AdminCartRulesController` and the newer `Discount` domain — this domain is currently query-only.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/CartRule/` — Queries, QueryResults, QueryHandlers, ValueObjects, Exceptions, Settings |
| Adapter | `src/Adapter/CartRule/QueryHandler/` — query handler implementations only |
| Legacy ObjectModel | `classes/CartRule.php` (2224 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/CartRuleController.php` |

## Non-obvious patterns

- **No Command subdirectory exists** — this domain is intentionally read-only in CQRS. Mutation is handled by the legacy controller or the `Discount` domain (`src/Core/Domain/Discount/`), which is the active successor for write operations.
- `CartRuleSettings.php` is `@deprecated` and marked for removal in 10.0 — do not add new constants here; use the Discount domain instead.
- `CartRuleId` is referenced by Cart and Order domains (add/remove cart rule commands, order discount views) — treat this ValueObject as a shared contract.

## Canonical examples

- `src/Core/Domain/CartRule/Query/SearchCartRules.php` + `src/Adapter/CartRule/QueryHandler/SearchCartRulesHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- [Discount Domain](../Discount/CONTEXT.md) — write operations for cart rules are handled here
