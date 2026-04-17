# Import Component

## Purpose

Multi-step, batch-capable CSV import pipeline: file upload/reading, column-to-field mapping, entity-type-specific handlers, and stateful progress tracking across multiple HTTP requests. Does not handle localization pack imports (`LocalizationPackImporter` is separate).

## Layers

| Layer | Path |
|-------|------|
| Core pipeline contracts | `src/Core/Import/` |
| Entity field metadata (9 types) | `src/Core/Import/EntityField/Provider/` |
| Adapter handlers + utilities | `src/Adapter/Import/` |
| Controller entry point | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/ImportController.php` |

## Non-obvious patterns

- `ImportRuntimeConfig` is **mutable and serializable** — its `toArray()` output is stored between HTTP requests to track offset/limit/progress across batched imports
- `ImportConfig` (immutable) vs `ImportRuntimeConfig` (mutable per batch) — two separate objects for a reason; don't merge them
- `ProductImportHandler` is the most complex handler — it imports combinations, features, images, suppliers, and specific prices all via legacy ObjectModels
- 9 supported entity types defined as constants in `Entity`; `ImportHandlerFinder` matches by `supports()` — no if/else chains

## Canonical examples

- `src/Core/Import/ImporterInterface.php` + `src/Core/Import/Importer.php`
- `src/Core/Import/Handler/ImportHandlerInterface.php`
- `src/Adapter/Import/Handler/AbstractImportHandler.php`

## Related

- [Export Component](../Export/CONTEXT.md) — counterpart for CSV generation
