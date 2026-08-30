# Title Domain

## Purpose

Manages customer salutation titles (e.g. Mr., Mrs.) including a small gender classification and an optional icon image. Does NOT manage customer data itself — Title is a lookup entity referenced by the Customer domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Title/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Title/` — handler implementations, `TitleRepository`, `TitleValidator`, `TitleImageThumbnailProvider` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShopParameters/TitleController.php`, `src/Core/Grid/Definition/Factory/TitleGridDefinitionFactory.php` |

## Non-obvious patterns

- Each title carries a `Gender` value object (male/female/neutral) used to drive pronoun logic in transactional emails — not merely a display label.
- Image upload is part of the domain: `TitleImageUploadingException` in Core and `TitleImageThumbnailProvider` in Adapter. `TitleSettings` defines the canonical default image dimensions (16×16 px).
- `AbstractTitleHandler` wraps the legacy `Gender` ObjectModel (not a Doctrine entity); persistence still goes through the legacy layer.

## Canonical examples

- `src/Core/Domain/Title/Command/AddTitleCommand.php` + `src/Adapter/Title/CommandHandler/AddTitleHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Title/` — Behat behavior scenarios
