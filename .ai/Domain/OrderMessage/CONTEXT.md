# OrderMessage Domain

## Purpose

Manages predefined order message templates that back-office agents can attach to orders when communicating with customers. Does not handle the actual sending of messages to customers (that is triggered from the Order domain).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/OrderMessage/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/OrderMessage/` — handler implementations, `AbstractOrderMessageHandler.php`, `OrderMessageProvider.php` |
| Legacy ObjectModel | `classes/order/OrderMessage.php` (52 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/CustomerService/OrderMessageController.php`, `src/Core/Grid/Definition/Factory/OrderMessageGridDefinitionFactory.php` |

## Non-obvious patterns

- `OrderMessageProvider` in the Adapter layer is a separate read-only helper (not a CQRS query handler) used to populate dropdowns in the Order view — it reads via the legacy ObjectModel directly.
- The domain is under **CustomerService** in the admin navigation, not under Orders, despite its name.

## Canonical examples

- `src/Core/Domain/OrderMessage/Command/AddOrderMessageCommand.php` + `src/Adapter/OrderMessage/CommandHandler/AddOrderMessageHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/OrderMessage/` — Behat behavior scenarios
