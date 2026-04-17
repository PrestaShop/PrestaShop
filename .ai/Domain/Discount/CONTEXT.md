# Discount Domain

## Purpose

Manages cart rules / discount vouchers: creation, update, duplication, deletion, and status toggling. Does NOT handle price calculation at checkout — the application of discounts to carts is a concern of the Cart/Pricing layer.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Discount/` — Commands, Queries, QueryResults, ValueObjects, Exceptions; also top-level `ProductRule*`, `ProductRuleGroup*`, `DiscountSettings.php` |
| Adapter | `src/Adapter/Discount/` — `Application/` (service), `CommandHandler/`, `QueryHandler/`, `Repository/`, `Trait/`, `Update/`, `Validate/` |
| Legacy ObjectModel | `classes/CartRule.php` (2224 lines) — the "Discount" domain maps to the `CartRule` ObjectModel; do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/DiscountController.php`, `src/Core/Grid/Definition/Factory/DiscountGridDefinitionFactory.php` |

## Non-obvious patterns

- The domain is called **Discount** but the underlying legacy ObjectModel is `CartRule`. The repository explicitly wraps `CartRule` instances — always use `DiscountId`, never raw cart rule IDs from outside this domain.
- `ProductRule`, `ProductRuleGroup`, `ProductRuleType`, and `ProductRuleGroupType` are defined directly at the `src/Core/Domain/Discount/` root (not inside a `ValueObject/` subfolder) — they model the condition sets that a discount can match against.
- `src/Adapter/Discount/Application/DiscountApplicationService.php` encapsulates the logic for computing the outcome of applying a discount; it is separate from the CQRS handlers.
- `src/Adapter/Discount/Update/` contains specialised updaters (`DiscountBuilder`, `DiscountConditionsUpdater`, `DiscountDuplicator`, `Filler/`) rather than a single monolithic handler — large commands delegate to these collaborators.

## Canonical examples

- `src/Core/Domain/Discount/Command/AddDiscountCommand.php` + `src/Adapter/Discount/CommandHandler/AddDiscountCommandHandler.php`
- `src/Adapter/Discount/Repository/DiscountRepository.php` — shows the CartRule wrapping pattern

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Discount/` — Behat behavior scenarios
- [GOTCHAS.md](../../GOTCHAS.md) — Discount↔CartRule naming mismatch, legacy class size warning
