# Position Domain

## Purpose

Provides shared value objects and exceptions for row-position ordering used across multiple domains (e.g. products). It does NOT own any entity — it exists purely as a cross-cutting structural utility.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Position/` — ValueObjects, Exceptions only (no Commands or Queries of its own) |

## Non-obvious patterns

- This domain has no Adapter layer, no legacy class, and no controller — it is a pure shared vocabulary used by other domains (e.g. `Product/Command/UpdateProductsPositionsCommand.php` imports `RowPosition`).
- Do not add commands or queries here; position-update commands live in the domain that owns the ordered entity (e.g. Product).

## Canonical examples

- `src/Core/Domain/Position/ValueObject/RowPosition.php` — referenced by `src/Adapter/Product/CommandHandler/UpdateProductsPositionsHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
