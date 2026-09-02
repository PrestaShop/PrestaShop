# Hook Domain

## Purpose

Manages Hook entity metadata and status (enabled/disabled) through CQRS. Does not implement hook dispatching or module registration — those live in `src/Adapter/Hook/HookDispatcher.php` and the legacy `Hook` class.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Hook/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Hook/` — handler implementations, `HookDispatcher.php`, `HookInformationProvider.php` |
| Legacy ObjectModel | `classes/Hook.php` (1616 lines) — do not add logic here |

## Non-obvious patterns

- The CQRS surface is intentionally narrow: only status toggling (`UpdateHookStatusCommand`) and read queries exist. Hook dispatching (calling module listeners) is handled entirely outside this domain via `HookDispatcher`.
- `src/Adapter/Hook/HookInformationProvider.php` provides hook metadata for the back-office hook debugger, not covered by any Query.

## Canonical examples

- `src/Core/Domain/Hook/Command/UpdateHookStatusCommand.php` + `src/Adapter/Hook/CommandHandler/`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
