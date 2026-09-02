# Alias Domain

## Purpose

Manages search aliases — alternative search terms that redirect to a canonical search query. Does not handle search indexing or product search ranking.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Alias/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Alias/` — handler implementations, repositories, validator |
| Legacy ObjectModel | `classes/Alias.php` (145 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShopParameters/SearchAliasController.php` |

## Non-obvious patterns

- The controller is named `SearchAliasController`, not `AliasController` — it lives under `ShopParameters`, not `Catalog`.
- `src/Adapter/Alias/Validate/AliasValidator.php` holds domain validation logic separate from the constraint constants pattern used in other domains.
- The CQRS commands/queries are mostly based on the `searchTerm`, not the DB ID

## Canonical examples

- `src/Core/Domain/Alias/Command/AddAliasCommand.php` + `src/Adapter/Alias/CommandHandler/AddAliasCommandHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
