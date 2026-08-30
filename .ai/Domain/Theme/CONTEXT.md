# Theme Domain

## Purpose

Manages front-office theme lifecycle: import, enable, delete, RTL adaptation, and layout reset. Does NOT handle theme configuration data (colors, fonts) — that lives in theme-specific config files outside the CQRS layer.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Theme/` — Commands, CommandHandlers (concrete, not interfaces), ValueObjects, Exceptions |
| Adapter | `src/Adapter/Theme/ThemeMultiStoreSettingsFormDataProvider.php` — multi-store form data only |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/Design/ThemeController.php` |

## Non-obvious patterns

- This domain has **no Query/QueryResult** — it is write-only via CQRS. Theme reading is done through the legacy `Theme` service layer, not through domain queries.
- Several `CommandHandler` implementations are **concrete classes directly in `src/Core/Domain/Theme/CommandHandler/`** (e.g. `DeleteThemeHandler`, `EnableThemeHandler`), not in `src/Adapter/`. This departs from the standard Core=interface / Adapter=implementation split.
- `ThemeImportSource` value object encapsulates three import strategies (archive upload, URL, FTP path) in one object using named static constructors.
- `AdaptThemeToRTLLanguagesCommand` triggers RTL CSS generation — it is only relevant when the shop has RTL languages installed.

## Canonical examples

- `src/Core/Domain/Theme/Command/ImportThemeCommand.php` + `src/Core/Domain/Theme/CommandHandler/ImportThemeHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [GOTCHAS.md](../../GOTCHAS.md) — concrete handlers live in Core (not Adapter), breaks standard pattern
