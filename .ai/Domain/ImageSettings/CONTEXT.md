# ImageSettings Domain

## Purpose

Manages image types (thumbnail dimensions per entity type) and global image settings (format, compression). Also handles thumbnail regeneration across all image domains. Does not store or serve actual image files.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/ImageSettings/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | handlers are co-located in `src/Core/Domain/ImageSettings/CommandHandler/` and `QueryHandler/` (no separate `src/Adapter/ImageSettings/`) |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/Design/ImageSettingsController.php` |

## Non-obvious patterns

- Several concrete handlers (`AddImageTypeHandler`, `EditImageTypeHandler`, `DeleteImageTypeHandler`, `RegenerateThumbnailsHandler`, etc.) live directly in `src/Core/Domain/ImageSettings/CommandHandler/` rather than in `src/Adapter/`. This domain mixes interface-only and concrete handlers in the same folder.
- `src/Core/Domain/ImageSettings/ImageDomain.php` is a PHP `enum` that enumerates which entity types can have thumbnails regenerated (`products`, `categories`, `manufacturers`, `suppliers`, `stores`, `all`). Pass it to `RegenerateThumbnailsCommand`.

## Canonical examples

- `src/Core/Domain/ImageSettings/Command/AddImageTypeCommand.php` + `src/Core/Domain/ImageSettings/CommandHandler/AddImageTypeHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/ImageSettings/` — Behat behavior scenarios
- [GOTCHAS.md](../../GOTCHAS.md) — concrete handlers live in Core (not Adapter), breaks standard pattern
