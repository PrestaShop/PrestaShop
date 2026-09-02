# Customer Domain

## Purpose

Manages shop customers: registration, profile editing, deletion methods, and group membership. Does NOT handle orders, cart rules, or addresses — those are separate domains.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Customer/` — Commands, Queries, QueryResults, ValueObjects, Exceptions; `Group/` sub-domain |
| Adapter | `src/Adapter/Customer/` — handler implementations, `CustomerDataProvider.php`, `CustomerDataSource.php`, `CustomerConfiguration.php`, `Group/`, `Repository/`, `Validate/` |
| Legacy ObjectModel | `classes/Customer.php` (1570 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Customer/CustomerController.php`, `src/Core/Grid/Definition/Factory/CustomerGridDefinitionFactory.php` (+ `CustomerGroupsGridDefinitionFactory`, `CustomerDiscountGridDefinitionFactory`, `CustomerOrderGridDefinitionFactory`) |

## Non-obvious patterns

- The `Group` sub-domain (`Core/Domain/Customer/Group/` and `Adapter/Customer/Group/`) is a first-class sub-domain, not a nested afterthought — customer groups have their own commands, handlers, and repository.
- `CustomerDeleteMethod` is a value object that encodes whether deletion anonymises or fully removes the customer — callers must pass it explicitly.
- `CustomerConfiguration.php` in the adapter exposes shop-level customer settings (e.g. registration mode) consumed by multiple handlers.
- Multiple grid factories exist for the customer detail page (orders, discounts, groups) in addition to the main listing grid.

## Canonical examples

- `src/Core/Domain/Customer/Command/AddCustomerCommand.php` + `src/Adapter/Customer/CommandHandler/AddCustomerCommandHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Customer/` — Behat behavior scenarios
