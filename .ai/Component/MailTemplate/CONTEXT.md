# MailTemplate Component

## Purpose

Infrastructure for discovering, rendering, and transforming transactional email templates: theme/layout discovery from the filesystem, Twig-based HTML rendering, and a composable transformation pipeline (CSS inlining, HTML-to-text, variable substitution). Does not send emails — that is the legacy `Mail` class.

## Layers

| Layer | Path |
|-------|------|
| Theme + layout discovery | `src/Core/MailTemplate/FolderThemeCatalog.php`, `FolderThemeScanner.php` |
| Rendering contracts + variable builder | `src/Core/MailTemplate/MailTemplateRendererInterface.php`, `src/Core/MailTemplate/Layout/LayoutVariablesBuilder.php` |
| Transformation pipeline | `src/Core/MailTemplate/Transformation/` |
| Adapter (Twig renderer + preview) | `src/Adapter/MailTemplate/` |

## Non-obvious patterns

- `CSSInlineTransformation` is **skipped for the `modern` theme** — the modern theme uses inline styles natively and running the transformer again would break it
- Modules contribute mail themes via `actionListMailThemes` hook, and transformations via `actionGetMailLayoutTransformations` — the pipeline is fully module-extensible
- `Layout` distinguishes core vs module templates via `CORE_CATEGORY` / `MODULES_CATEGORY` constants — not by path prefix

## Canonical examples

- `src/Core/MailTemplate/FolderThemeCatalog.php`
- `src/Adapter/MailTemplate/MailTemplateTwigRenderer.php`
- `src/Core/MailTemplate/Transformation/CSSInlineTransformation.php`

