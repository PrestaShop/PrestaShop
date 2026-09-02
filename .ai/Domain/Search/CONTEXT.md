# Search Domain

## Purpose

Handles product search indexation (triggering full or partial reindexing) for the back-office. It does NOT implement front-office search UI rendering — that is handled by `src/Core/Product/Search/` and `SearchProductSearchProvider`.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Search/` — Commands, CommandHandler interface, Exceptions only (no Queries) |
| Adapter | `src/Adapter/Search/` — `SearchIndexationHandler`, `SearchProductSearchProvider` (front-office, unrelated to CQRS) |
| Legacy ObjectModel | `classes/Search.php` (1273 lines) — do not add logic here |

## Non-obvious patterns

- The domain is command-only (no Query/QueryResult). Reindexation is a fire-and-forget operation.
- `SearchIndexationCommand` accepts an optional `ProductId` and `ShopConstraint`, allowing single-product or shop-scoped reindexation in addition to full reindex.
- `src/Adapter/Search/SearchProductSearchProvider.php` is a front-office concern that happens to live in this adapter but has nothing to do with the CQRS indexation flow.
- `SearchIndexationHandlerInterface` is declared in Core (`CommandHandler/`), not in Adapter — the adapter provides the concrete implementation.

## Canonical examples

- `src/Core/Domain/Search/Command/SearchIndexationCommand.php` + `src/Adapter/Search/CommandHandler/SearchIndexationHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Shop Domain](../Shop/CONTEXT.md) — `ShopConstraint` is used to scope indexation
- `tests/Integration/Behaviour/Features/Scenario/Search/` — Behat behavior scenarios
