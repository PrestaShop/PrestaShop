# CQRS Component

## Purpose

Command Bus and Query Bus infrastructure (built on Symfony Messenger) that decouples controllers from business logic. Does not define any business commands or queries — those live in each domain's `Command/` and `Query/` directories.

## Layers

| Layer | Path |
|-------|------|
| Bus interface | `src/Core/CommandBus/CommandBusInterface.php` |
| Handler attributes + compiler pass | `src/PrestaShopBundle/DependencyInjection/Compiler/CommandAndQueryRegisterPass.php` |
| Domain interfaces (per domain) | `src/Core/Domain/{Domain}/CommandHandler/` + `QueryHandler/` |
| Concrete handlers (per domain) | `src/Adapter/{Domain}/CommandHandler/` + `QueryHandler/` |

## Non-obvious patterns

- Both buses share the same `CommandBusInterface` — differentiated only by the message type passed, not separate interfaces
- `#[AsCommandHandler]` / `#[AsQueryHandler]` attributes on adapter classes auto-register via the compiler pass; it infers the handled message type from the handler method's first parameter type hint
- Grid query builders are **not** dispatched through the bus — they query the DB directly
- `ExecutedCommandRegistry` tracks all dispatched commands with backtraces in debug mode

## Canonical examples

- `src/Core/CommandBus/CommandBusInterface.php`
- `src/Adapter/Hook/CommandHandler/UpdateHookStatusCommandHandler.php`
- `src/PrestaShopBundle/Controller/Admin/PrestaShopAdminController.php` — `dispatchCommand()` / `dispatchQuery()` helpers

## Related

- [Forms Component](../Forms/CONTEXT.md) — `FormDataHandler` implementations dispatch commands via `CommandBusInterface`
- [Grid Component](../Grid/CONTEXT.md) — grids dispatch queries for data fetching
