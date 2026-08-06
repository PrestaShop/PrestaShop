# OrderReturn Domain

## Purpose

Manages merchandise return requests (RMA) initiated by customers: allows back-office agents to view return details and update the return state. Does not manage return state definitions (see OrderReturnState domain) or the customer-facing return form.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/OrderReturn/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/OrderReturn/` — handler implementations, `Validator/OrderReturnValidator.php`, `Repository/OrderReturnRepository.php` |
| Legacy ObjectModel | `classes/order/OrderReturn.php` (252 lines) — do not add logic here |

## Non-obvious patterns

- The only write operation in this domain is `UpdateOrderReturnStateCommand` — creation of returns is customer-facing and handled outside the CQRS layer.
- There is no dedicated back-office controller for OrderReturn; order return management is surfaced within the Order detail view and via a legacy admin controller.
- `OrderReturnValidator` in the Adapter layer enforces business rules (e.g., cannot change state of a closed return) separately from the command itself.

## Canonical examples

- `src/Core/Domain/OrderReturn/Command/UpdateOrderReturnStateCommand.php` + `src/Adapter/OrderReturn/CommandHandler/UpdateOrderReturnStateHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [OrderReturnState Domain](../OrderReturnState/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/OrderReturn/` — Behat behavior scenarios
