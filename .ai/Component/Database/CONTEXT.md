# Database Component

## Purpose

Repository abstraction layer between domain handlers and persistence: `AbstractObjectModelRepository` wraps legacy ObjectModel CRUD inside typed domain exceptions. Does not define any business entities — those live in each domain's adapter layer.

## Layers

| Layer | Path |
|-------|------|
| Core repository base | `src/Core/Repository/AbstractObjectModelRepository.php` |
| Multi-shop variant | `src/Core/Repository/AbstractMultiShopObjectModelRepository.php` |
| Domain repositories (30+) | `src/Adapter/{Domain}/Repository/` |
| Legacy DB adapter | `src/Adapter/Database.php` |

## Non-obvious patterns

- `AbstractObjectModelRepository` wraps all `PrestaShopException`s in typed `CoreException` sub-classes — handlers never catch raw PS exceptions
- `partiallyUpdateObjectModel(array $properties)` — partial updates pass an explicit list of fields to `ObjectModel::update()`; avoids overwriting unloaded fields
- `softDeleteObjectModel()` vs `deleteObjectModel()` — soft delete sets `active=0` rather than deleting the row; used for entities with referential integrity constraints
- `Db::getInstance()` is never used in new repositories — inject `Doctrine\DBAL\Connection` instead

## Canonical examples

- `src/Core/Repository/AbstractObjectModelRepository.php`
- `src/Adapter/Order/Repository/OrderRepository.php`

## Related

- [CQRS Component](../CQRS/CONTEXT.md) — handlers inject repositories to fulfil commands/queries
