# Tab Domain

## Purpose

Manages back-office navigation tabs (menu entries) and their active/inactive status, keyed by controller class name. Does not manage front-office menus or access control permissions (those belong to the Authorization domain).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Tab/` — Commands, ValueObjects, Exceptions (no Query/QueryResult — reads use the adapter data provider) |
| Adapter | `src/Adapter/Tab/` — CommandHandler, `TabDataProvider.php` |
| Legacy ObjectModel | `classes/Tab.php` (622 lines) — do not add logic here |

## Non-obvious patterns

- The domain has **no Query/QueryHandler layer**: read access is provided exclusively by `src/Adapter/Tab/TabDataProvider.php`, which wraps legacy `Tab` ObjectModel calls directly. New queries should follow the standard pattern rather than extending the data provider.
- The single command targets tabs **by class name** (`UpdateTabStatusByClassNameCommand`), not by numeric ID — this is the canonical way to toggle a tab programmatically from other domains.
- There is no back-office controller for Tab: visibility toggles are triggered as side-effects from other admin operations (e.g., enabling a module registers its tabs).

## Canonical examples

- `src/Core/Domain/Tab/Command/UpdateTabStatusByClassNameCommand.php` + `src/Adapter/Tab/CommandHandler/`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [GOTCHAS.md](../../GOTCHAS.md) — Tab string identity (class name, not numeric ID)
