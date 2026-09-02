# Meta Domain

## Purpose

Manages SEO metadata (page title, description, keywords, URL rewrite rules) for CMS and system pages. Also covers shop URL configuration and friendly-URL schema settings, though those are adapter-only concerns with no dedicated CQRS commands.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Meta/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Meta/` — handler implementations, `MetaDataProvider.php`, `MetaEraser.php`, and several `DataConfiguration` classes for URL/SEO settings |
| Legacy ObjectModel | `classes/Meta.php` (512 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShopParameters/MetaController.php` |

## Non-obvious patterns

- The adapter contains multiple `DataConfiguration` classes (`SEOOptionsDataConfiguration`, `SetUpUrlsDataConfiguration`, `ShopUrlDataConfiguration`, `UrlSchemaDataConfiguration`) that manage related shop-URL and friendly-URL settings via the legacy `Configuration` table — these have no corresponding CQRS commands and are handled directly by the controller.
- `AbstractMetaCommand` provides a shared base for `AddMetaCommand` and `EditMetaCommand`; prefer extending it if adding new write commands.
- No delete command exists in the CQRS layer; deletion is handled via `MetaEraser` in the adapter.
- `GetPagesForLayoutCustomization` query returns pages that can have custom layouts, coupling Meta to the theme layout customization feature.

## Canonical examples

- `src/Core/Domain/Meta/Command/AddMetaCommand.php` + `src/Adapter/Meta/CommandHandler/`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Meta/` — Behat behavior scenarios
