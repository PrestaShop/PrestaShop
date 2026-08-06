# SearchEngine Domain

## Purpose

Manages search engine URL parameter mappings (the back-office "Search Engines" configuration listing known web crawlers and their query-string keys). It does NOT implement product search indexation — see the Search domain for that.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/SearchEngine/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/SearchEngine/` — handler implementations, `AbstractSearchEngineHandler` |
| Legacy ObjectModel | `classes/SearchEngine.php` (64 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShopParameters/SearchEnginesController.php` |

## Non-obvious patterns

- Despite the name overlap, this domain is entirely unrelated to `src/Core/Domain/Search/` — SearchEngine configures crawler recognition, Search triggers product reindexation.
- The controller is named `SearchEnginesController` (plural), consistent with its list/CRUD nature.

## Canonical examples

- `src/Core/Domain/SearchEngine/Command/AddSearchEngineCommand.php` + `src/Adapter/SearchEngine/CommandHandler/` (handler)
- `src/Core/Domain/SearchEngine/Query/GetSearchEngineForEditing.php` + `src/Core/Domain/SearchEngine/QueryResult/SearchEngineForEditing.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/SearchEngine/` — Behat behavior scenarios
