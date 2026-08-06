# Tax Domain

## Purpose

Manages individual tax rates (percentage + name) applied to products and orders. Does NOT handle tax rule grouping (see TaxRulesGroup) or ecotax calculation logic beyond reset utility.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Tax/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Tax/` — handler implementations, `TaxComputer`, ecotax sub-package |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/International/TaxController.php`, `src/Core/Grid/Definition/Factory/TaxGridDefinitionFactory.php` |

## Non-obvious patterns

- Handlers extend `AbstractTaxHandler` which wraps the legacy `Tax` ObjectModel — there is no Doctrine repository; all persistence goes through the legacy ObjectModel.
- `src/Adapter/Tax/Ecotax/ProductEcotaxResetter.php` implements `Core\Tax\Ecotax\ProductEcotaxResetterInterface` (defined outside the Domain folder in `src/Core/Tax/`), used when resetting ecotax on products — this is a cross-cutting concern, not a CQRS command.
- `src/Core/Tax/TaxOptionsConfiguration.php` sits outside the Domain folder and handles the global "enable taxes" configuration setting.

## Canonical examples

- `src/Core/Domain/Tax/Command/AddTaxCommand.php` + `src/Adapter/Tax/CommandHandler/AddTaxHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Tax/` — Behat behavior scenarios
