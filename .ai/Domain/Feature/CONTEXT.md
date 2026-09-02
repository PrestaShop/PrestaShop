# Feature Domain

## Purpose

Manages product Features and their Values (e.g. "Composition: Cotton"), which are displayed on product pages. Does not handle Product-level feature associations — those live in `src/Core/Domain/Product/FeatureValue/`.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Feature/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Feature/` — handler implementations, repository, feature-flag helpers |
| Legacy ObjectModel | `classes/Feature.php` (354 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/FeatureController.php` |

## Non-obvious patterns

- `src/Adapter/Feature/` contains `FeatureFeature.php`, `CombinationFeature.php`, `GroupFeature.php`, `MultistoreFeature.php` — these implement `FeatureInterface` and are feature-flag helpers (not Feature domain entities).
- `src/Core/Domain/Feature/FeatureValueSettings.php` holds domain constants (e.g. max value length) that don't warrant a ValueObject.
- Feature and FeatureValue share the same domain folder; both have full CRUD commands and separate ValueObjects (`FeatureId` / `FeatureValueId`).

## Canonical examples

- `src/Core/Domain/Feature/Command/AddFeatureCommand.php` + `src/Adapter/Feature/CommandHandler/` (handler implementation)

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Feature/` — Behat behavior scenarios
