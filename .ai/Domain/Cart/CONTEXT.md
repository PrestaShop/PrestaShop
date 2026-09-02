# Cart Domain

## Purpose

Manages the shopping cart lifecycle: creation, product additions/removals, quantity updates, cart rule application, address/carrier/currency settings, and price computation. Does not handle order creation — that is the Order domain's responsibility.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Cart/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Cart/` — handler implementations, `CartRepository`, `CartProductsComparator` |
| Calculation engine | `src/Core/Cart/` — pure price/tax/discount computation, independent of persistence |
| Legacy ObjectModel | `classes/Cart.php` (5 000+ lines), `classes/CartRule.php` — do not add logic here |
| Presenter | `src/Adapter/Presenter/Cart/` — lazy-loading Smarty presentation layer |
| Back-office UI | `CartController.php`, `CartGridDefinitionFactory`, `CartSummaryType`, `cart-editor.ts` |

## Non-obvious patterns

- The **calculation engine** (`src/Core/Cart/`) is separate from the CQRS domain layer — `Calculator` orchestrates totals purely in memory; adapter handlers call it before persisting
- Cart→Order conversion happens via `AddOrderFromBackOfficeCommand` in the **Order domain**, which takes a `CartId`; the Cart domain has no knowledge of this
- `AbstractCartHandler` in Adapter provides shared cart-loading and validation used by all command handlers

## Canonical examples

- `src/Core/Domain/Cart/Command/AddProductToCartCommand.php` + `src/Adapter/Cart/CommandHandler/AddProductToCartHandler.php`
- `src/Core/Cart/Calculator.php` — calculation engine entry point

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Order Domain](../Order/CONTEXT.md) — converts `CartId` into an order
- [Forms Component](../../Component/Forms/CONTEXT.md) — `CartSummaryType`, `AddOrderCartRuleType`
- [Grid Component](../../Component/Grid/CONTEXT.md) — `CartGridDefinitionFactory`
- [DevDocs](https://devdocs.prestashop-project.org/9/development/architecture/domain/references/cart/#cart-domain/)
- `tests/Integration/Behaviour/Features/Scenario/Cart/` — Behat behavior scenarios
