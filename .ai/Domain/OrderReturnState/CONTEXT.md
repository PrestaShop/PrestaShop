# OrderReturnState Domain

## Purpose

Manages the configurable set of states that a merchandise return (RMA) can pass through (e.g., "Waiting for package", "Package received"). Does not manage individual order returns themselves (see OrderReturn domain).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/OrderReturnState/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/OrderReturnState/` — handler implementations, `AbstractOrderReturnStateHandler.php`, `Repository/OrderReturnStateRepository.php`, `OrderReturnStateDataProvider.php` |
| Legacy ObjectModel | `classes/order/OrderReturnState.php` (47 lines) — do not add logic here |
| Back-office UI | `src/Core/Grid/Definition/Factory/OrderReturnStatesGridDefinitionFactory.php` (no dedicated Symfony controller found; managed via legacy admin controller) |

## Non-obvious patterns

- `OrderReturnStateSettings` at the domain root defines configuration constants (e.g., max color length) shared across commands — check it before adding new field constraints.
- The `Name` ValueObject is shared with OrderState but lives in its own namespace — do not import across domains.

## Canonical examples

- `src/Core/Domain/OrderReturnState/Command/AddOrderReturnStateCommand.php` + `src/Adapter/OrderReturnState/CommandHandler/AddOrderReturnStateHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- [OrderReturn Domain](../OrderReturn/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/OrderReturnState/` — Behat behavior scenarios
