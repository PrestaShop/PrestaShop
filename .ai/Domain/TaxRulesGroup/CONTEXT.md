# TaxRulesGroup Domain

## Purpose

Manages tax rule groups — named collections of tax rules that associate a tax rate with a country/state/zip range. Does NOT manage individual Tax rates (see Tax domain) or the tax rule rows within a group (handled via legacy admin).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/TaxRulesGroup/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/TaxRulesGroup/` — handler implementations, `TaxRulesGroupRepository`, `TaxRulesGroupValidator` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/International/TaxRulesGroupController.php`, `src/Core/Grid/Definition/Factory/TaxRulesGroupGridDefinitionFactory.php` |

## Non-obvious patterns

- `TaxRulesGroupId` is referenced cross-domain: `Product` (pricing), `Carrier` (`SetCarrierTaxRuleGroupCommand`), confirming it is a shared foreign key used for tax computation on catalog items.
- Status toggle uses `SetTaxRulesGroupStatusCommand` / `BulkSetTaxRulesGroupStatusCommand` (not `Toggle*`) — asymmetric naming compared to Tax domain.
- A separate `TaxRuleGridDefinitionFactory` exists for the individual rules within a group; those rules have no CQRS commands and are still managed via legacy ObjectModel.

## Canonical examples

- `src/Core/Domain/TaxRulesGroup/Command/AddTaxRulesGroupCommand.php` + `src/Adapter/TaxRulesGroup/CommandHandler/AddTaxRulesGroupHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/TaxRulesGroup/` — Behat behavior scenarios
