# MailTemplate Domain

## Purpose

Handles generation of transactional email templates from Twig themes into static HTML/text files on disk. Does not manage sending emails — that belongs to the `Mail` legacy class. Does not store template records in the database.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/MailTemplate/` — single command (`GenerateThemeMailTemplatesCommand`) and its handler/interface |
| Core services | `src/Core/MailTemplate/` — theme scanning (`FolderThemeCatalog`), rendering pipeline, layout system, and CSS/text transformations |
| Adapter | `src/Adapter/MailTemplate/` — `MailTemplateTwigRenderer.php`, `MailPartialTemplateRenderer.php`, `MailPreviewVariablesBuilder.php` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/Design/MailThemeController.php` |

## Non-obvious patterns

- The domain CQRS layer is unusually thin (one command only). The real complexity is in `src/Core/MailTemplate/`, which implements a full theme/layout/transformation pipeline independently of the CQRS command.
- `GenerateThemeMailTemplatesHandler` uses `ThemeCatalogInterface` to scan Twig theme folders and `MailTemplateRendererInterface` to render each layout, writing HTML and plain-text files. No database writes occur.
- Transformations (`src/Core/MailTemplate/Transformation/`) apply CSS inlining and variable substitution to rendered output before writing to disk.

## Canonical examples

- `src/Core/Domain/MailTemplate/Command/GenerateThemeMailTemplatesCommand.php` + `src/Core/Domain/MailTemplate/CommandHandler/GenerateThemeMailTemplatesHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
