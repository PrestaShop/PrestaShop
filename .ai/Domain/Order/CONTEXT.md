# Order Domain

## Purpose

Manages the full lifecycle of back-office orders: creation, status changes, product additions/removals, refunds, returns, payments, invoices, shipping, and cart-rule management. Does not own the checkout flow (that belongs to the Cart/Checkout domain).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Order/` — Commands, Queries, QueryResults, ValueObjects, Exceptions; includes sub-domains `Invoice/`, `Payment/`, `Product/` |
| Adapter | `src/Adapter/Order/` — handler implementations, `Refund/`, `Delivery/`, `Checkout/`, `Repository/`, `AbstractOrderHandler.php`, `OrderAmountUpdater.php`, `OrderDetailUpdater.php`, `OrderProductQuantityUpdater.php` |
| Legacy ObjectModel | `classes/order/Order.php` (2701 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Order/OrderController.php`, `src/Core/Grid/Definition/Factory/OrderGridDefinitionFactory.php` |

## Non-obvious patterns

- The domain contains three internal sub-domains under `src/Core/Domain/Order/`: `Invoice/`, `Payment/`, and `Product/`, each with their own Commands, CommandHandlers, and Exceptions.
- `AbstractOrderHandler` in the Adapter layer provides shared legacy-ObjectModel helpers (loading Order, Product, Combination, Currency, etc.) used by multiple command handlers.
- Refund logic is split across four dedicated classes in `src/Adapter/Order/Refund/`: calculator, updater, slip creator, and voucher generator — refund commands delegate to this subsystem rather than implementing logic inline.
- `CancellationActionType` defines four mutually exclusive cancellation strategies (cancel, standard refund, partial refund, return) used by `AbstractRefundCommand` subclasses.
- `src/Adapter/Order/Delivery/` and `src/Adapter/Order/GiftOptionsConfiguration.php` handle shop-configuration concerns, not per-order mutations.
- This domain heavily relies on the legacy Cart and `Context::getContext()` stateful approach. A `ContextStateManager` component was developed to manage this — CQRS handlers use it to temporarily set context state during command execution.

## Canonical examples

- `src/Core/Domain/Order/Command/UpdateOrderStatusCommand.php` + `src/Adapter/Order/CommandHandler/UpdateOrderStatusHandler.php`
- `src/Core/Domain/Order/Query/GetOrderForViewing.php` + `src/Adapter/Order/QueryHandler/GetOrderForViewingHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Order/` — Behat behavior scenarios
