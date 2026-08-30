# PositionUpdater Component

## Purpose

Drag-and-drop position reordering for back-office grid rows: receives position modifications (old → new), resolves conflicts, re-indexes cleanly, and persists via Doctrine DBAL. Lives inside the Grid component's source tree but usable by any entity that supports manual ordering.

## Layers

| Layer | Path |
|-------|------|
| Core contracts + updater | `src/Core/Grid/Position/` |
| DBAL persistence handler | `src/Core/Grid/Position/UpdateHandler/DoctrinePositionUpdateHandler.php` |

## Non-obvious patterns

- `PositionDefinitionInterface` maps the update to a DB table (`getTable()`, `getIdField()`, `getPositionField()`, `getParentIdField()`) — supports hierarchical positioning (e.g. categories within a parent via `getParentIdField()`)
- Re-indexing starts from `getFirstPosition()` — some entities start at 0, others at 1; always check the definition
- The updater validates that no two items end up at the same position before persisting — if validation fails it throws `PositionUpdateException`, not a silent no-op

## Canonical examples

- `src/Core/Grid/Position/GridPositionUpdater.php`
- `src/Core/Grid/Position/PositionDefinitionInterface.php`

## Related

- [Grid Component](../Grid/CONTEXT.md) — position updater lives in the Grid source tree
- [CQRS Component](../CQRS/CONTEXT.md) — position updates are dispatched as CQRS commands
