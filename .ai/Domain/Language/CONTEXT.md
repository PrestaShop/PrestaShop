# Language Domain

## Purpose

Manages store languages: adding, editing, toggling status, and deleting. Also handles language pack download/installation from PrestaShop's translation server and RTL stylesheet generation.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Language/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Language/` — handler implementations, repository, and specialized helpers |
| Legacy ObjectModel | `classes/Language.php` (1795 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/International/LanguageController.php` |

## Non-obvious patterns

- `src/Adapter/Language/` contains infrastructure beyond handlers: `LanguagePackInstaller.php`, `LanguageCopier.php`, `LanguageActivator.php`, `LanguageImageManager.php`, `LanguageFlagThumbnailProvider.php`, `LanguageValidator.php`, and `ContextLanguageProvider.php` — these are all support services, not handlers.
- `src/Adapter/Language/Pack/LanguagePackImporter.php` handles downloading and importing translation packs from the API; this is invoked by command handlers rather than being part of the domain core.
- `src/Adapter/Language/RTL/InstalledLanguageChecker.php` supports right-to-left stylesheet injection, a cross-cutting concern triggered on language save.

## Canonical examples

- `src/Core/Domain/Language/Command/AddLanguageCommand.php` + `src/Adapter/Language/CommandHandler/`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Language/` — Behat behavior scenarios
