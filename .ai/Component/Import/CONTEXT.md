# Import Component

## Purpose

Multi-phase, batch-capable import engine for the BO **Advanced Parameters > Import** page: file upload/normalization, column-to-field mapping, per-entity importers driven by a phase model (validate → insert/update → associate → finalize), and a server-side `ImportRun` aggregate tracking progress across HTTP requests. Does not handle localization pack imports (`LocalizationPackImporter` is separate).

**Under active refactoring** (#41907). The target architecture below is the source of truth for the ongoing PR series; [PLAN.md](PLAN.md) tracks the roadmap, per-field decisions, and progress. Anything marked *deprecated* must not be built upon.

## Layers

| Layer | Path | Status |
|-------|------|--------|
| Engine contracts (importers, phases, registry) | `src/Core/Import/Engine/` | existing (PR1) |
| Engine reads/fallback writes | existing `src/Adapter/*/Repository/*` classes | existing (PR1) — no engine-owned lookup layer; plain-`int` lookup methods added to the Adapter repositories; forced-id creation + `date_add` live on `Adapter ProductRepository` (`createWithForcedId`, `setDateAdd`) |
| Per-entity importers | `src/Core/Import/Engine/EntityImporter/` | existing (PR1: Product) |
| Entity field metadata (value objects) | `src/Core/Import/EntityField/` | VOs kept; provider services deprecated as importers embed their field lists |
| File handling (readers, uploader, Excel→CSV) | `src/Core/Import/File/` | existing, kept |
| CQRS `ImportRun` aggregate (commands, query, VOs) | `src/Core/Domain/Import/` | existing, reworked in PR2 |
| `ImportRun` handlers, lock | `src/Adapter/Import/CommandHandler/`, `.../QueryHandler/` | existing, reworked in PR2 |
| `ImportRun` entity + repository | `src/PrestaShopBundle/Entity/ImportRun.php` | existing, reworked in PR2 |
| Old orchestration — `Importer`, `Configuration/`, `Handler/` | `src/Core/Import/` (non-kept parts) | **deprecated, do not use** |
| Old entity handlers (ObjectModel ports) | `src/Adapter/Import/Handler/` | **deprecated, do not use** |
| Symfony controller (step-1 page only is live) | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/ImportController.php` | partial, wired in later PR |
| Legacy controller (still the live engine) | `controllers/admin/AdminImportController.php` | replaced progressively, removed next major |

## Non-obvious patterns

- **The "migrated" pre-#41907 system was never switched on.** Step-1 submit forwards to the legacy controller; the Symfony step-2 controller, `Core\Import\Importer` and both `Adapter\Import\Handler\*` classes are unreachable code. They are line-by-line legacy ports (ObjectModel, validation entangled with insertion) — deprecated, never extend them.
- **Phase model.** Each entity importer declares an ordered subset of phases: `validation`, `database`, `association`, `finalization`. Self-referencing data (product accessories, category parents) is resolved in `association`, *after* every row has been inserted — so row order in the file never matters, including A↔B mutual references. `finalization` covers whole-dataset work (category ntree rebuild). Phase definitions (technical id + translatable label) live **in code only**; the DB stores phase-id strings. Phase ids are open strings — the four common ids are conventions, and importers may declare custom phases (e.g. a combinations pre-phase generating attributes).
- **Single run with pausing phases.** A phase may be declared *pausing* (validation is): when it completes having produced any message (warning, or error meaning rows that will be skipped), the run stops with status `awaiting_confirmation`; a clean pausing phase continues in the same run without pause — no click needed for a clean file. Calling `RunImportBatch` on an `awaiting_confirmation` run implicitly accepts the messages and resumes; `CancelImportRun` is the reject path. `options.dryRun` truncates the phase list after `validation` (API validate-only; the BO does not use it).
- **One state shape.** `RunImportBatch` returns the same `ImportRunState` DTO as the `GetImportRunState` query — polling and state reads share one structure.
- **Store-and-skip invalid rows.** Validation scans the whole file; invalid row indexes are stored (sparse, per phase) and later phases skip them; the import finishes even with errors. An internal sanity cap (~10k invalid rows) fails the run instead ("file malformed" — wrong separator/encoding). All persisted state is bounded by constants (caps), never by file size — no per-row table.
- **Association existence is checked twice.** (1) End of validation: one request builds an in-memory hashed identity set from the file's identity columns, then verifies every association target against set + DB — nothing persisted; skippable per-run for huge files (not recommended). (2) At `association` phase resolution, defensively (DB may have changed; only check when (1) was skipped) — failures are errors but the run completes.
- **Existence-failure severity is per-field policy owned by the importer**: some references auto-create (product categories, manufacturers, features — legacy behavior kept), others warn-and-drop (suppliers — an address is required to create one; accessories).
- **Batching resumes by row position + opaque reader cursor.** At `StartImportRun` the file is normalized once (encoding → UTF-8, Excel → CSV, BOM stripped) into a run-scoped working file that always uses one canonical CSV dialect (`;`, `"` — constants on `CsvImportFileNormalizer`): the user-chosen separator AND the configured skip rows are consumed at normalization — the engine reader is dialect-free and the working file holds data records only (row indexes = 0-based data-record indexes; presenters add the skip count back for source-file line numbers). The engine tracks progress in row positions; after each batch the reader returns an opaque resume cursor persisted on the run (the CSV reader's cursor is a byte offset for `fseek` — no O(n²) re-scan; other formats define their own), which the engine never interprets. One batch budget may span a phase boundary; per-phase unit counts are computed ONCE at phase entry (`countPhaseUnits()` → stored on the context by `enterPhase()`, 0 units → phase skipped) and default to the working file's record count, which the normalization pass itself measured (`ImportRunContext::getDataRecordCount()`) — importers never read the file to count; completion = last phase's offset reaching its total. The resumable reader is a standalone interface yielding plain `array<int, string>` records (the legacy `DataRow` layer is deprecated). Importers extend `AbstractEntityImporter` (batch loop, phase guard, default unit count) — implementing the interface directly stays supported.
- **Hybrid persistence, no ObjectModel.** Importers dispatch existing CQRS commands for everything commands cover; a narrow import-specific repository handles the two gaps (forced-ID creation for the *force IDs* option, `date_add`). This keeps importers in `Core`. The batch sequencer dispatching commands through the bus is a sanctioned composition service — it does not violate the "handlers never call handlers" rule (nothing injects another handler).
- **Registry by service tag.** Importers are tagged services collected into `EntityImporterRegistry`: one source of truth for the BO entity dropdown, the mapping page's field list, and batch dispatch. The tag is applied automatically to every **autoconfigured** service implementing `EntityImporterInterface` (`registerForAutoconfiguration` in `PrestaShopExtension` — a class-level attribute is not needed and `#[AutoconfigureTag]` on the interface is ignored by Symfony 6.4). Modules register importers the same way: an autoconfigured service definition is enough (see the `demoentityimporter` example module).
- **Bounded messages.** Run messages are structured (`severity, phase, row, field, message`), capped per severity, with overflow counters — reporting works identically for a 100-row and a 5M-row file.
- **Cleanup.** On any terminal status (`finished`/`cancelled`/`failed`): skipped-row lists, shared data and the resume cursor are cleared and the working file deleted; capped messages are kept for the post-run report. Old run rows are garbage-collected two ways: an opportunistic purge inside `StartImportRun` (terminal runs older than the retention constant, plus their leftover working files) and a console purge command for real cron setups.
- **Reader abstraction.** CSV only today; the reader interface is deliberately narrow so a Symfony-Serializer-based reader (e.g. JSON with native multi-language fields) can be added without touching importers.

## Canonical examples

- `src/Core/Import/EntityField/Provider/EntityFieldsProviderInterface.php` — kept field-metadata contract
- `src/Core/Import/File/FileReaderInterface.php` — kept generator-based reader contract
- `src/Core/Domain/Import/Command/StartImportRunCommand.php` — run creation carrying the frozen config + mapping
- `src/Adapter/Import/CommandHandler/RunImportBatchHandler.php` — batch entry point (becomes the phase sequencer in PR2)
- `src/Core/Import/Engine/EntityImporterInterface.php` — importer contract (spec in [PLAN.md](PLAN.md))
- `src/Core/Import/Engine/EntityImporter/ProductImporter.php` — reference importer implementation (phases, command dispatch, association resolution)
- `tests/Integration/Core/Import/Engine/ImportEngineTestRunner.php` — minimal phase sequencer showing how a caller drives an importer

## Related

- [PLAN.md](PLAN.md) — refactoring roadmap, architecture spec, per-field decisions, progress (temporary file, removed when the migration completes)
- [Export Component](../Export/CONTEXT.md) — counterpart for CSV generation
- [CQRS Component](../CQRS/CONTEXT.md) — command/handler conventions the importers rely on
- [Behat Component](../Behat/CONTEXT.md) — integration test conventions for the run lifecycle
- Issue [#41907](https://github.com/PrestaShop/PrestaShop/issues/41907), PR [#41911](https://github.com/PrestaShop/PrestaShop/pull/41911) (held, reworked as PR2)
