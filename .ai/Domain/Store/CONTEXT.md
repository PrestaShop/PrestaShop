# Store Domain

## Purpose

Manages physical store locations (contact info, hours, map coordinates) displayed to customers on the front office. Does not handle stock, warehouse logistics, or multi-shop configuration.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Store/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Store/` — CommandHandler, QueryHandler |
| Legacy ObjectModel | `classes/Store.php` (193 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShopParameters/StoreController.php`, `src/Core/Grid/Definition/Factory/StoreGridDefinitionFactory.php` |

## Non-obvious patterns

- The domain repository (`src/Core/Domain/Store/Repository/StoreRepository.php`) lives inside `Core/Domain/` rather than `Adapter/`, extending `AbstractObjectModelRepository` — it is the Core-side repository interface used directly by handlers.
- No dedicated form type exists in the bundle; the back-office form is handled via the legacy controller path.

## Canonical examples

- `src/Core/Domain/Store/Command/DeleteStoreCommand.php` + `src/Adapter/Store/CommandHandler/`
- `src/Core/Domain/Store/Query/GetStoreForEditing.php` + `src/Adapter/Store/QueryHandler/`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Store/` — Behat behavior scenarios
