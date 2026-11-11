# Search Domain

## Purpose

Handles product search indexation (triggering full or partial reindexing, counting indexed products) for the back-office. It does NOT implement front-office search UI rendering — that is handled by `src/Core/Product/Search/` and `SearchProductSearchProvider`.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Search/` — `SearchIndexationCommand`, `GetIndexedProductsCount` query, handler interfaces, Exceptions |
| Adapter | `src/Adapter/Search/` — handlers, `Repository/IndexedProductsRepository`, `Configuration/*` (settings of *Shop Parameters > Search*), `SearchProductSearchProvider` (front-office, unrelated to CQRS) |
| Legacy ObjectModel | `classes/Search.php` (1273 lines) — do not add logic here |

## Non-obvious patterns

- Reindexation is a fire-and-forget command. The only query, `GetIndexedProductsCount`, takes a `ShopConstraint` and counts active products visible in search (`both`/`search`): with a single shop, that shop's product settings; otherwise each product once, with its default shop settings (legacy `Shop::addSqlAssociation()` parity).
- The search settings (`PS_SEARCH_*`) are plain `ps_configuration` values handled by `AbstractMultistoreConfiguration` classes in `src/Adapter/Search/Configuration/` — no CQRS command.
- The cron indexation URL is anonymous and protected by a slice of the cookie key (`AdminSearchController::TOKEN_CHECK_*`); keep that slice unchanged or merchants' existing crontabs break. The new cron route deliberately has no `_legacy_link`: legacy cron URLs keep hitting `AdminSearch` directly instead of getting a redirect that a cron HTTP client may not follow.
- `SearchIndexationCommand` accepts an optional `ProductId` and `ShopConstraint`, allowing single-product or shop-scoped reindexation in addition to full reindex.
- `src/Adapter/Search/SearchProductSearchProvider.php` is a front-office concern that happens to live in this adapter but has nothing to do with the CQRS indexation flow.
- `SearchIndexationHandlerInterface` is declared in Core (`CommandHandler/`), not in Adapter — the adapter provides the concrete implementation.

## Canonical examples

- `src/Core/Domain/Search/Command/SearchIndexationCommand.php` + `src/Adapter/Search/CommandHandler/SearchIndexationHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Shop Domain](../Shop/CONTEXT.md) — `ShopConstraint` is used to scope indexation
- `tests/Integration/Behaviour/Features/Scenario/Search/` — Behat behavior scenarios
