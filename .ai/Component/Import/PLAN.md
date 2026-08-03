# Import migration — architecture spec & roadmap

> **Temporary working document** for the #41907 refactoring. It is the source of truth for the PR series below; update the checklists as PRs land. **Delete this file when the migration reaches GA** — durable knowledge belongs in [CONTEXT.md](CONTEXT.md).
>
> **Audience: implementation sessions with no memory of the design conversation.** Together with [CONTEXT.md](CONTEXT.md), this file must contain everything needed to start PR1/PR2/PR3 from scratch — codebase map, baseline branch state, contracts, per-field decisions. If you change the architecture while implementing, update this file in the same PR.
>
> Last updated: 2026-08-03 (PR1 implemented on the same PR: engine + ProductImporter + tests; decisions 15–17 added, per-field decisions resolved — see the checklist and the mapping table).

## Decision log

1. **Abandon the old orchestration** (`Core\Import\Importer`, `Configuration\ImportRuntimeConfig`, `Handler\*`, `Adapter\Import\Handler\*`, session-coupled `@=` wiring). It was never switched on (step-1 forwards to legacy), covers 2/9 entities as ObjectModel ports, and entangles validation with insertion. Mark `@deprecated` in PR1; removal timing decided later (BC promise likely forces next major).
2. **Salvage the clean pieces**: `Core\Import\EntityField/**` **value objects** (`EntityField`, collection — the 8 provider *services* are deprecated once importers embed their field lists), `File/**` (readers, uploader, Excel→CSV), exception hierarchy, `Adapter\Import\{ImportDataFormatter, ImportEntityDeleter}`. `Core\Import\Configuration\**` (`ImportConfig` + `ImportRuntimeConfig` + their interfaces and factories) is fully deprecated — `ImportRunContext` is the single runtime object (see contract), built from the Doctrine entity, no factories. `ImageCopier` is replaced by a small engine `ImageDownloader` (URL/path → local temp file); image association + thumbnail generation belong to the CQRS image commands.
3. **New engine namespace**: `Core\Import\Engine\` (sibling of the salvaged pieces).
4. **Hybrid persistence**: importers dispatch existing CQRS commands; a narrow import-specific repository covers only the gaps — forced-ID creation (*force IDs* option) and `date_add`. No ObjectModel → importers live in `Core`.
5. **Invalid rows: store-and-skip, always.** Validation scans the whole file; invalid row indexes are stored sparse per phase; later phases skip them; the run finishes even with errors. Internal sanity cap (~10 000 invalid rows): beyond it the run fails ("file appears malformed"). Persisted state is bounded by constants, never file size — no per-row table.
6. **Single run with pausing phases** (supersedes the earlier two-run flow — validation would have executed twice). A phase may be flagged *pausing* (validation is; future phases may be too, e.g. an association pre-validation): when it completes **with any message** (warning, or error meaning rows that will be skipped), the run stops as `awaiting_confirmation` so the BO modal / API client can review; a clean pausing phase continues in the same run without pause (legacy-like: no click for a clean file). Re-calling `RunImportBatch` on an `awaiting_confirmation` run **implicitly accepts** the messages and advances to the next phase; `CancelImportRun` rejects. Accepted caveat: a naive polling client auto-confirms. `options.dryRun` (engine-level, unused by the BO) truncates the phase list after `validation` for API validate-only — needed because clean files never pause. A dry run with errors still ends `finished` (reporting was its job); a full run fails only on sanity-cap breach or fatal error.
7. **Association existence checked twice**: (a) end-of-validation in-memory sub-step (nothing persisted; per-run skip option for huge files, off by default); (b) defensive re-check at `association`-phase resolution (errors, run still completes).
8. **Existence-failure severity is per-field policy** owned by each importer (auto-create vs warn-and-drop vs error) — see behavior inventory.
9. **Cleanup & GC**: terminal status clears `skipped_rows`, `shared_data`, `resume_cursor` and deletes the working file; capped `messages` kept for the post-run report. GC of old run rows, two mechanisms (core has no cron): (a) opportunistic purge in `StartImportRunHandler` — one indexed `DELETE` of terminal runs older than the retention constant, plus their leftover working files; (b) a `prestashop:import:purge-runs` console command for ops/real cron.
10. **PR #41911 held open**, reworked as PR2 once the engine (PR1) is merged.
11. **One state shape**: `RunImportBatchCommand` returns the same `ImportRunState` DTO as `GetImportRunState` — both are "the up-to-date state of the run"; `ImportBatchReport` is dropped.
12. **No session between wizard steps**: step-1 POSTs to step-2, the config travels as hidden inputs in the mapping form, and the final POST dispatches `StartImportRun` with everything (the file itself is already in the import directory from the upload AJAX). Supersedes the issue's "session kept pre-start" statement.
13. **Row position + opaque resume cursor**: the engine tracks progress in row positions (format-agnostic); after each batch the reader returns an opaque cursor persisted on the run (`resume_cursor`) — the CSV reader's cursor is a byte offset for `fseek`, a future JSON reader defines its own (split files, item index). The engine never interprets it. `CsvFileReader` is refactored directly with an additive resumable interface — no wrapper class.
14. **String entity ids end-to-end**: the legacy `Core\Import\Entity::TYPE_*` int mapping is dropped — the ints only feed deprecated code (step-1 form choices, session config, int-keyed provider finder, current `EntityType` VO), `ps_import_match` presets don't store an entity, and the feature flag being off means no production rows exist. `entity_type` column becomes a string in PR2.
15. **Canonical working-file dialect** (PR1 review): the normalizer rewrites EVERY input (CSV with the user's separator, or spreadsheet) into one canonical CSV dialect (`;`, `"`, empty escape, UTF-8, no BOM, blank lines preserved for row-index parity). The user-chosen separator is consumed once at normalization; the engine reader takes no per-run dialect (kills the session-coupled ctor wiring), `readFrom()` needs no dialect params.
16. **Lookups are Core `@internal` services, no interfaces** (PR1 review): the engine's name/path/reference lookups are concrete `final` classes in `Core\Import\Engine\Repository\` injecting DBAL `Connection` directly (precedent: Core Grid query builders), documented `@internal` — not meant to be overridden or decorated. Only the fallback **writer** keeps the Core-interface → Adapter-implementation split (`ProductImportWriterInterface` → `Adapter\Import\Repository\ProductImportWriter`, which needs ObjectModel `force_id`).
17. **Importer auto-tagging via the interface** (PR1 review): no per-implementation attribute — `PrestaShopExtension` calls `registerForAutoconfiguration(EntityImporterInterface::class)->addTag(...)` (`#[AutoconfigureTag]` on an interface is ignored by Symfony 6.4), so any autoconfigured service implementing the interface is collected, module services included (their definitions must set `autoconfigure: true`). Proven by the `demoentityimporter` module in the example-modules repository.

## Codebase map — audit snapshot (develop, 2026-07-30)

### Old partially-migrated system (never switched on)

Step-1 submit saves to the session then 307-forwards to the legacy controller (`ImportController::importAction` — the redirect to step 2 is commented out in favor of `forwardRequestToLegacyResponse()`). Everything downstream of step 1 in the Symfony stack is therefore dead code.

| Area | Path | Fate |
|---|---|---|
| Orchestration: `Importer`, `ImporterInterface`, `Configuration/ImportRuntimeConfig*` | `src/Core/Import/` | **deprecated** (validate/insert entangled; browser-driven statelessness) |
| `Configuration/ImportConfig*` + interfaces + factories | `src/Core/Import/Configuration/` | **deprecated** — `ImportRunContext` is the single runtime object (decision 2) |
| Handler contracts (`ImportHandlerInterface`, finder) | `src/Core/Import/Handler/` | **deprecated** (single `importRow()`, `validateOnly` bool threaded through handlers) |
| Field metadata: `EntityField`, collection, 8 providers (category, product, combination, customer, address, supplier, alias, store contact), finder | `src/Core/Import/EntityField/` | VOs kept; provider *services* deprecated as importers embed their field lists (their data is copied into each importer's `getFields()`); the int-keyed-YAML finder replaced by the registry; note: **no Manufacturer provider exists** (bug below) |
| File handling: `CsvFileReader` (fgetcsv generator), `FileUploader`/`FileFinder`/`FileRemoval`, `ImportDirectory`, `DataRow`/`DataCell` (preview) | `src/Core/Import/File/` | kept — reader refactored in place with the cursor-resume interface (decision 13); `utf8_encode()` call replaced by the normalization step |
| Entity handlers: `AbstractImportHandler`, `ProductImportHandler` (~1270 lines), `CategoryImportHandler` (~540 lines), `ImportHandlerFinder` | `src/Adapter/Import/Handler/` | **deprecated** — line-by-line ObjectModel ports; never build on them |
| Utilities: `CsvFileOpener` (Excel→CSV), `ImportDataFormatter` (`getBoolean`/`getPrice`/`split`/`createMultiLangField`), `ImportEntityDeleter` (truncate), `DataMatchSaver` | `src/Adapter/Import/` | kept |
| `ImageCopier` | `src/Adapter/Import/` | **deprecated** — replaced by engine `ImageDownloader` (download only); association + thumbnails via CQRS image commands |
| Controllers: `ImportController` (step-1 page + upload/delete/download/sample/fields AJAX are **live**; `processImportAction` dead), `ImportDataConfigurationController` (step 2 — dead but complete) | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/` | wired for real in the UI PR |
| Forms: `ImportType`, `ImportDataConfigurationType`, `ImportFormDataProvider` | `src/PrestaShopBundle/Form/Admin/Configure/AdvancedParameters/Import/` | reused in UI PR — session writes removed: step-1 config POSTs through step-2 as hidden inputs (decision 12) |
| TS batch driver: `Importer.ts`, `ImportBatchSizeCalculator` (targets ~5 s/request, limit clamped 5–100), `PostSizeChecker`, `ImportProgressModal`, … | `admin-dev/themes/new-theme/js/pages/import-data/` | good engineering, reusable for the UI PR (payload shrinks: runId only) |
| Service wiring | `.../config/services/core/import.yml`, `.../services/adapter/import.yml` | old blocks use session-coupled `@=` expression arguments — **do not reproduce that pattern** |

### Legacy controller — behavior reference for iso-functional parity

`controllers/admin/AdminImportController.php` (~4 200 lines). Dispatcher `importByGroups()`, AJAX entry `ajaxProcessImport()` (params `offset`/`limit`/`validateOnly`/`moreStep` + `crossStepsVars` JSON round-trip), driven by `js/admin/import.js` (two full passes: validateOnly then real).

| Entity | Batch / per-row methods |
|---|---|
| Categories | `categoryImport` / `categoryImportOne` |
| Products | `productImport` / `productImportOne` + deferred `productImportAccessories` (the `moreStep` pass) |
| Combinations | `attributeImport` / `attributeImportOne` |
| Customers | `customerImport` / `customerImportOne` |
| Addresses | `addressImport` / `addressImportOne` |
| Brands | `manufacturerImport` / `manufacturerImportOne` |
| Suppliers | `supplierImport` / `supplierImportOne` |
| Alias | `aliasImport` / `aliasImportOne` |
| Store contacts | `storeContactImport` / `storeContactImportOne` |

Key helpers to consult when porting semantics: `getBoolean`/`getPrice`/`split`/`createMultiLangField` (statics, mirrored in `ImportDataFormatter`), `fillInfo` (multilang rule: write target lang, or any lang whose current value is empty), `copyImg` → `ImageManager::copyImg` (URL or local path, always outputs `.jpg`), `truncateTables`, `openCsvFile`/`utf8EncodeArray` (encoding horrors the normalizer replaces).

### Baseline — branch `41907-import-domain-layer` (PR #41911, held, reworked in PR2)

- **Domain** `src/Core/Domain/Import/`: `StartImportRunCommand` (full step-1+2 config incl. column mapping + frozen batch `limit`; ctor validation codes 1–8 in `ImportRunConstraintException`), `RunImportBatchCommand(runId)`, `CancelImportRunCommand`, `GetImportRunState` query → `ImportRunState` DTO; `ImportBatchReport` DTO; VOs `ImportRunId` (UUID v4 string), `EntityType` (**wraps legacy `Core\Import\Entity` ints — the leak PR2 removes**), `ImportOptions`, `ColumnMapping`, `ImportRunStatus` (`pending|running|finished|cancelled`).
- **Adapter** `src/Adapter/Import/CommandHandler|QueryHandler/`: handlers via `#[AsCommandHandler]`/`#[AsQueryHandler]`; `ImportRunRepository` (`add`/`save`/`getById`); per-run non-blocking Symfony Lock (`FlockStore`, key `import-run-<uuid>`, fail fast → `ImportRunAlreadyRunningException`). `RunImportBatchHandler` currently delegates to the deprecated `Core\Import\Importer` + `ImportHandlerFinder` — that is what PR2 replaces with the sequencer.
- **Entity/schema**: `src/PrestaShopBundle/Entity/ImportRun.php` → `ps_import_run` (UUID varchar(36) PK; `entity_type` int; `filename`, `lang_iso`, `csv_separator`, `multiple_value_separator`, `skip_rows`; `field_map`/`options`/`shared_data` JSON; `validate_only`, `batch_limit`, `current_offset`, `total_rows`; `status`; flat `errors`/`warnings`/`notices` JSON; `id_shop`; dates). In `install-dev/data/db_structure.sql` only.
- **Feature flag** `import` (beta, state 0) in `install-dev/data/xml/feature_flag.xml`; no consumer yet.
- **Behat**: suite `import` (`tests/Integration/Behaviour/behat.yml`), 6 lifecycle/constraint scenarios in `Features/Scenario/Import/import_run.feature`, fixture `tests/Resources/import/dummy.csv`; no batch-execution scenario (old executor needs a web context — unblocked once importers are CQRS-based).

## Architecture spec

### Importer contract (`Core\Import\Engine`)

```php
interface EntityImporterInterface
{
    public function getEntityType(): string;                      // 'product' — string id; legacy Entity::TYPE_* int mapping kept for wizard BC
    public function getFields(): EntityFieldCollectionInterface;  // fields embedded in the importer (EntityField VOs); old provider services deprecated
    /** @return list<ImportPhaseDefinition> ordered — ids are open strings; validation/database/association/finalization are conventions */
    public function getPhases(): array;
    /** recomputed at phase entry; 0 => phase skipped */
    public function countPhaseUnits(ImportPhaseDefinition $phase, ImportRunContext $context): int;
    /** process up to $limit units from the phase's stored position */
    public function processPhaseBatch(ImportPhaseDefinition $phase, ImportRunContext $context, int $limit): PhaseBatchResult;
}
```

- `ImportPhaseDefinition` — technical id + translatable label + `isPausing` flag (a pausing phase completing with any message stops the run as `awaiting_confirmation` — see decision 6). **Code-defined only, never persisted**; the DB stores phase-id strings. Ids are **open strings** — the `PHASE_*` constants only name the common four; importers may declare custom phases (e.g. a combinations `attribute_generation` pre-phase). A stored id no longer matching `getPhases()` (deploy changed the list mid-run) fails the run gracefully.
- `ImportRunContext` — **the single runtime/config object** (replaces the deprecated `ImportConfig`/`ImportRuntimeConfig` pair and their factories): a plain core object mirroring the `ImportRun` entity's structure without depending on Doctrine, built from the entity by the adapter. Carries the frozen config, reader position (row + resume cursor), skipped-row set, options, shop constraint.
- `PhaseBatchResult` — processed unit count, structured messages, newly skipped row indexes, new resume cursor.
- `EntityImporterRegistry` — collects tagged importer services (`#[AutoconfigureTag]`); powers the BO entity dropdown, the mapping-page field list, and batch dispatch. Module-extensible.

### Proposed engine layout (PR1)

```
src/Core/Import/Engine/
├── EntityImporterInterface.php
├── EntityImporterRegistry.php            # tagged services 'core.import.entity_importer'
├── ImportPhaseDefinition.php             # PHASE_VALIDATION|PHASE_DATABASE|PHASE_ASSOCIATION|PHASE_FINALIZATION + label wording
├── ImportRunContext.php
├── PhaseBatchResult.php
├── ImportMessage.php                     # severity, phase, row, field, message
├── Exception/                            # UnknownEntityTypeException, UnknownPhaseException, MalformedImportFileException, …
├── File/
│   └── ImportFileNormalizer.php          # UTF-8 + Excel→CSV → run-scoped working file
│       # (Core\Import\File\CsvFileReader gains the cursor-resume interface directly — no wrapper)
├── ImageDownloader.php                   # URL/path → local temp file (replaces ImageCopier; CQRS commands own association + thumbnails)
└── EntityImporter/
    ├── ProductImporter.php               # dispatches commands via Core\CommandBus\CommandBusInterface
    └── Product/…                         # field mapper / association resolver as implementation demands
```

Core/Adapter split rule (amended per decision 16): importers stay in Core (they only talk to the command bus). Read-only lookups are Core `@internal` DBAL services under `Core\Import\Engine\Repository\` (`ProductLookup`, `CategoryLookup`, `ManufacturerLookup`, `SupplierLookup`, `ShopLookup`, `LanguageLookup`, `FeatureLookup`, `TaxRulesGroupLookup` — the last one wraps `Adapter\Tax\TaxComputer`). Only the fallback writer keeps the interface split: `Core\Import\Engine\Repository\ProductImportWriterInterface` → `src/Adapter/Import/Repository/ProductImportWriter.php` (forced-ID insert via ObjectModel `force_id` + `date_add`).

### Batch sequencing (PR2, reworked `RunImportBatchHandler`)

```
if run.status == awaiting_confirmation:            # re-call = implicit accept (decision 6)
    resume -> status running
budget = run.batchLimit
while budget > 0 and run not finished:
    remaining = phaseTotal(current_phase) - current_offset
    if remaining == 0:                              # phase exhausted
        if phase.isPausing and phase produced messages:
            status = awaiting_confirmation; stop    # next RunImportBatch resumes, Cancel rejects
        if run.options.dryRun and current_phase == validation:
            markFinished(); stop                    # API validate-only
        advance to next phase; countPhaseUnits() -> phase_totals; reset offset + resume cursor; continue
    result = importer.processPhaseBatch(phase, context, min(budget, remaining))
    persist offset / resume cursor / messages / skipped rows; budget -= result.processedUnits
finished when the LAST phase's offset >= its total   // replaces the "short batch" heuristic
```

`RunImportBatch` returns the up-to-date `ImportRunState` (same DTO as the query — decision 11). Status lifecycle: `pending → running ⇄ awaiting_confirmation → finished | cancelled | failed`.

One batch budget may span a phase boundary (e.g. "validate last 2 rows + insert first 3"). Global progress = Σ phase offsets / Σ phase totals — approximate (totals recomputed at boundaries), monotonic. Per-phase progress is exact; the modal shows both bars.

Sequencer notes:
- **Truncate timing**: `options.truncate` executes **once at `database` phase entry** — i.e. after validation completed and any pause was confirmed — via the kept `ImportEntityDeleter`. Never in `dryRun`, never on a run cancelled at the pause, never re-executed on later batches (phase entry happens once). Same semantics as legacy (truncate at offset 0 of the real pass, skipped in validateOnly).
- **Phase skipping via `countPhaseUnits`**: an importer skips a phase cheaply by returning 0 — e.g. `association` returns 0 when no association column is mapped; otherwise it may simply count all data rows and fast-skip cells without values.
- **Phase totals count data rows** (after `skip_rows`) — legacy's `total_rows` counted physical lines including headers; fix in PR2.

### File handling

- `StartImportRun` normalizes the upload **once** into a run-scoped working file: encoding → UTF-8 (replaces the deprecated `utf8_encode()` and the legacy whole-file `mb_check_encoding` on every batch), Excel → CSV (fixing the legacy converter's forced `;` and stale filename cache), BOM stripped. **The working file always uses the canonical CSV dialect** (`;`, `"`, empty escape — constants on `ImportFileNormalizer`; decision 15): the user's separator is consumed at normalization, the engine reader is dialect-free, blank lines are preserved so row indexes stay aligned with the source. The working file lives next to the upload in the import directory, named by run id (e.g. `<runId>.work.csv`), and is deleted at terminal state.
- Batches resume by **row position + opaque reader cursor** persisted on the run (`resume_cursor` column): the engine asks the reader to resume at row N and hands back the last cursor; the CSV reader's cursor is a byte offset (`fseek`, O(1) resume — total O(n) per phase, not O(n²)); a future JSON reader defines its own (split files at init, item index). The engine never interprets the cursor.
- Reader stays behind `FileReaderInterface`, extended in place with the resumable contract (additive — `CsvFileReader` refactored directly, no wrapper); a Symfony-Serializer-based reader (JSON, native multi-language) can be added later without touching importers.

### Association existence checks

- **Validation sub-step** (end of phase 1, one request): scan 1 builds an in-memory hashed identity set from the file's identity columns (products: `id`, `reference`); scan 2 verifies every association target against set + DB. Memory ≈ tens of MB per million rows (documented bound); nothing persisted. Skippable per run via `options.skipAssociationPrecheck` (off by default in BO, not recommended). If files ever exceed the memory bound, an opt-in persisted hash index can be added later without changing the contract.
- **Association phase** re-resolves each target against the DB at write time (covers DB drift and the skipped-pre-check case). Unresolved target → **error naming the association**, link dropped, run completes.

### `ps_import_run` schema deltas (PR2)

| Column | Content | Bound |
|---|---|---|
| `current_phase` varchar(32) | technical phase id | constant |
| `current_offset` (exists) | unit index within current phase; resets at phase entry | constant |
| `resume_cursor` varchar(255) | opaque reader cursor (CSV: byte offset) — replaces any byte-offset column | constant |
| `phase_totals` JSON | per-phase unit counts, computed at phase entry | ~4 entries |
| `skipped_rows` JSON | `{"validation":[…],"database":[…]}` sparse row indexes | sanity cap (~80 KB max) |
| `messages` JSON | `{severity, phase, row, field, message}` objects, capped at 1 000 per severity (replaces flat `errors`/`warnings`/`notices`) | cap constant |
| `message_counts` JSON | totals beyond the cap ("12 340 errors, first 1 000 shown") | 3 ints |
| `entity_type` | int → **string id** (decision 14) | — |
| `status` | gains `awaiting_confirmation` (pausing phase) + `failed` (sanity cap breach / fatal / unmatched phase id); lifecycle `pending → running ⇄ awaiting_confirmation → finished / cancelled / failed` | — |

`shared_data` kept as escape hatch but the phase model makes it obsolete (no more `crossStepsVars` / accessory map / `cat_moved` — phases re-derive from file + DB). `validate_only` column **dropped** — replaced by `options.dryRun` (phase-list truncation, decision 6).

### Known constraints (stated, accepted)

- **Hooks fire per CQRS command** (legacy defers them via `Module::setBatchMode` for products). Slower on big files — accepted for the iso-functional first pass; benchmark in PR1, mitigation (grouped/bulk paths per the #41321 spike) deferred.
- **No wrapping transaction per row**: a runtime command failure mid-row leaves a partially imported entity (row marked failed, remaining commands skipped, structured error). Legacy has no transactions either; validation phase makes this rare.
- **"Handlers never call handlers"**: the sequencer dispatching commands through the bus is a sanctioned composition service (nothing injects another handler).
- CQRS-based importers need no legacy web context → PR2 Behat can finally exercise batch execution headlessly (the blocker recorded in #41911).

## PR roadmap

### PR1 — engine + ProductImporter (from `develop`, no CQRS layer) — **done 2026-08-03** (same PR as the docs, #42247)

- [x] Engine contracts: `EntityImporterInterface`, `ImportPhaseDefinition` (open string ids + `pausing` flag), `ImportRunContext` (single runtime object), `PhaseBatchResult`, `ImportMessage`, `EntityImporterRegistry` (+ tag via `registerForAutoconfiguration`, decision 17), engine exceptions
- [x] File normalization service (UTF-8 + Excel→CSV → run-scoped working file, canonical dialect per decision 15) + cursor-resumable reading (`CsvFileReader` refactored in place, additive `ResumableFileReaderInterface` — CSV cursor = byte offset, generator yields cursor as key)
- [x] `ProductImporter` (`validation` / `database` / `association`) — fields embedded via `getFields()` (no provider service), field→command mapping below
- [x] `ImageDownloader` (URL/path → local temp file) — replaces `ImageCopier`, which gets `@deprecated`
- [x] Import-specific repository fallback: forced-ID creation, `date_add` (narrow, documented)
- [x] Association pre-check sub-step (in-memory identity set) + `skipAssociationPrecheck` option
- [x] `@deprecated` on the abandoned classes (lists in decisions 1–2) — annotated (27 files), all still wired; removal comes later
- [x] Integration tests (`tests/Integration/Core/Import/Engine/`) driving the importer through a mini-sequencer (batch limit 2 → cursor resume everywhere) with CSV fixtures (`tests/Resources/import/`): create (every mapped column asserted), update via `forceIDs` and `match_ref`, mutual accessories (A↔B in one file) + `@clear@`, invalid rows skipped + reported (zero validation writes), multilang single-language file, images from local fixture paths, virtual product, features
- [x] Hook-per-command benchmark: `IMPORT_BENCHMARK=1 phpunit --filter ProductImporterBenchmarkTest` — **1 000 rows in 30.4 s ≈ 33 rows/s** (5-column shape ≈ 3 commands/row, local macOS dev box, 2026-08-03); mitigation decision before GA still open
- [x] Keep `.ai/Component/Import/` docs in sync

PR1 field-decision notes (resolved 2026-08-03, details in the mapping table / behavior inventory):
- `quantity` → read current stock (`ProductLookup::getStockQuantity`), dispatch `setDeltaQuantity(target − current)`, skip when 0 (the stock command is delta-only by design).
- `supplier` → **no auto-create** (warn-and-drop): `AddSupplierCommand` requires an address the file cannot provide; existing suppliers still resolve by id/name.
- `customizable`/`uploadable_files`/`text_fields` → one real generic customization field per requested kind via `SetProductCustomizationFieldsCommand`; `customizable` alone → warning.
- `low_stock_alert` → follows the command coupling (alert = threshold ≠ 0); contradicting file value → warning.
- Multilang UPDATE writes the file's language only (legacy fill-empty-languages dropped); creation still duplicates into every language.
- `is_virtual` on update only converts TO virtual; an explicit 0 never converts back (protects existing types/downloads).
- `shop` column: `SetProductShopsCommand` with the run's shop guaranteed in the list (it holds the just-written data).
- Lookup additions beyond the spec'd set: `LanguageLookup` (iso→id, all-language duplication), `FeatureLookup` (feature/value by name).

### PR2 — CQRS rework (rebase/rework #41911 on the engine)

- [ ] Schema deltas above (`db_structure.sql`; upgrade SQL only if the flag ships enabled anywhere)
- [ ] `RunImportBatchHandler` → phase sequencer (drop `Core\Import\Importer` / `ImportHandlerFinder` reliance); pause flow (`awaiting_confirmation`, implicit resume on re-call, `Cancel` rejects); returns `ImportRunState` (drop `ImportBatchReport`)
- [ ] `StartImportRun`: file normalization, working file, phase totals, `options.dryRun`; terminal-state cleanup; opportunistic GC (retention constant) + `prestashop:import:purge-runs` console command
- [ ] `EntityType` VO → string ids only, no legacy int mapping (decision 14; removes the Domain → `Core\Import\Entity` leak); `entity_type` column → string
- [ ] Structured `messages` + caps (1 000/severity) + counters; `failed` status; cancel-vs-lock interplay
- [ ] Behat: multiple feature files — run lifecycle, product create (basic-fields CSV), product update (`forceIDs` / `match_ref` CSVs), accessories incl. mutual A↔B (dedicated CSV), pause/confirm/cancel flow, skip behavior + sanity cap, dry run; one fixture CSV per scenario family under `tests/Resources/import/`, every mapped column asserted after import

### PR3 — remaining entities

- [ ] `CategoryImporter` (+ `finalization` phase: single-unit ntree rebuild — also fixes legacy's never-regenerated non-AJAX path)
- [ ] Combination, Customer, Address, Manufacturer (+ its missing fields provider — see bugs), Supplier, Alias, StoreContact importers
- [ ] Per-entity Behat scenarios + CSV fixtures asserting every mapped column and associations

### Later (separate issues)

- [ ] Symfony controller/UI wiring: step-1 → step-2 config passed as hidden inputs (no session storage, decision 12), step-2 mapping screen, progress modal (per-phase + global bars, confirm/cancel dialog on `awaiting_confirmation`), `_legacy_feature_flag: import` routes, completion email on run finish, `_PS_MODE_DEMO_` guard + truncate super-admin gate at controller level
- [ ] Playwright coverage (#41922), then GA (flag promotion)
- [ ] Deferred per the #41321 spike: DBAL bulk paths, Messenger/async executor

## ProductImporter — field → command mapping (66 legacy columns)

| CSV column(s) | Phase | Target |
|---|---|---|
| `no` | — | ignored-column marker |
| `id` | database | match key when *force IDs*; forced-ID creation via fallback repository |
| `reference` | database | match key when *match ref* (shop-scoped lookup → update); also product detail (`UpdateProductCommand`) |
| `name`, `description`, `description_short`, `meta_title`, `meta_description`, `link_rewrite`, `available_now`, `available_later`, `delivery_in_stock`, `delivery_out_stock` | database | `AddProductCommand` (create) / `UpdateProductCommand` — localized (single-language file duplicated per legacy rule) |
| `active`, `visibility`, `condition`, `online_only`, `show_price`, `available_for_order`, `available_date`, `on_sale` | database | `UpdateProductCommand` (options) |
| `price_tex` / `price_tin`, `id_tax_rules_group`, `wholesale_price`, `unity`, `unit_price`, `ecotax` | database | `UpdateProductCommand` (prices; `price_tin` de-taxed via resolved tax group, `price_tex` wins when both present; ecotax zeroed unless `PS_USE_ECOTAX`; tax rules group must pre-exist — warning, no auto-create) |
| `width`, `height`, `depth`, `weight`, `additional_shipping_cost` | database | `UpdateProductCommand` (shipping) |
| `ean13`, `isbn`, `upc`, `mpn` | database | `UpdateProductCommand` (details) |
| `manufacturer` | database | resolve by id/name, auto-create via `AddManufacturerCommand` → `UpdateProductCommand` (manufacturerId) |
| `category` | database | resolve by id / name-path (`/` hierarchy), auto-create per missing path level via `AddCategoryCommand` → `SetAssociatedProductCategoriesCommand` (default = first entry) |
| `quantity`, `location`, `out_of_stock`, `minimal_quantity`, `low_stock_threshold`, `low_stock_alert` | database | `UpdateProductStockAvailableCommand` (location, out_of_stock; quantity converted to a delta from the current stock — the command is delta-only) / `UpdateProductCommand` (minimal_quantity, low_stock_threshold; alert derived = threshold ≠ 0, contradicting file value → warning) |
| `supplier`, `supplier_reference` | database | resolve by id/name — **no auto-create** (warn-and-drop; `AddSupplierCommand` needs address/city/country the file cannot provide) → `SetSuppliersCommand` + `UpdateProductSuppliersCommand` + `SetProductDefaultSupplierCommand` |
| `tags` | database | `SetProductTagsCommand` |
| `features` (`Name:Value:Position[:Custom]`) | database | resolve, auto-create via `AddFeatureCommand` / `AddFeatureValueCommand` → `SetProductFeatureValuesCommand` |
| `image`, `image_alt`, `delete_existing_images` | database | `ImageDownloader` (URL/path → temp file) → `AddProductImageCommand` / `UpdateProductImageCommand` (legend) / `DeleteProductImageCommand` |
| `is_virtual`, `file_url`, `nb_downloadable`, `date_expiration`, `nb_days_accessible` | database | `UpdateProductTypeCommand` (virtual) + `AddVirtualProductFileCommand` |
| `customizable`, `uploadable_files`, `text_fields` | database | legacy set bare counters; the importer creates one real generic customization field per requested kind (FILE for uploadable_files, TEXT for text_fields) via `SetProductCustomizationFieldsCommand`; `customizable` alone → warning |
| `reduction_price`, `reduction_percent`, `reduction_from`, `reduction_to` | database | `AddSpecificPriceCommand` (legacy "basic reduction": single rule, all currencies/countries/groups, from qty 1) |
| `shop` | database | shop id or name → `SetProductShopsCommand` / `ShopConstraint` on commands |
| `date_add` | database | fallback repository (not expressible via commands; `date_upd` always forced to now, per legacy) |
| `accessories` | **association** | resolve ids-or-references in DB → `SetRelatedProductsCommand` |

**Update-vs-create decision** (legacy `productImportOne` parity, per row): (1) `match_ref` on **and** `reference` matches an existing product (shop-scoped lookup) → update that product; (2) else `id` present **and** the product exists → update it; (3) else create — via the fallback repository with the forced id when `forceIDs` is on and `id` is present, via `AddProductCommand` otherwise. `date_upd` is always now; `date_add` only settable through the fallback repository.

## Behavior inventory — keep / change / fix

| Legacy behavior | Decision |
|---|---|
| Auto-create missing category (name/path), manufacturer, feature + feature value | **Keep** (iso-functional) |
| Auto-create missing supplier | **Change** → warn-and-drop (resolved in PR1: `AddSupplierCommand` requires address/city/country the file cannot provide; a bare legacy-style supplier row is not reachable through commands) |
| Unknown *numeric* category creates a stub **with that forced id** under Home | **Change** → error at validation (creating ids as a side effect is a trap) — confirm in PR1 review |
| Unknown accessory target silently dropped | **Change** → warning + drop (validation pre-check + association-phase error) |
| Accessories cannot be cleared (delete only runs when row has ≥1) | **Change** → explicit clear marker: a cell containing exactly `@clear@` empties the association (special characters make real-data collision unlikely; convention for multi-value association fields) |
| Multilang: one language per file (`iso_lang`); first import duplicates the value into every language | **Keep** for creation (JSON reader later enables true multi-language) |
| Multilang on UPDATE: legacy `fillInfo` also wrote any language whose current value was empty | **Change** → updates write the file's language only (resolved in PR1: fill-if-empty would need a per-row read of current localized values) |
| `low_stock_alert` independent of `low_stock_threshold` | **Change** → alert follows the command coupling (enabled ⇔ threshold ≠ 0, same as the BO product page); contradicting file value → warning (resolved in PR1) |
| `forceIDs` off → `id` column discarded entirely | **Keep** |
| `match_ref`: shop-scoped reference lookup → update | **Keep** (legacy uses two divergent lookup helpers — unify deliberately) |
| Validation pass writes to DB (attribute groups, `Feature::cleanPositions`, store images) | **Fixed by design** — validation phase performs no writes |
| `Feature::cleanPositions()` on every row, even validate-only | **Fixed by design** (dropped; commands manage positions) |
| Empty CSV row aborts the whole batch (`EmptyDataRowException` uncaught) | **Fixed by design** — empty row = skipped + notice |
| `getBoolean()` bare `(bool)` cast — `"false"`/`"no"` → `true` | **Change** → tolerant parser (`0/1/true/false/yes/no`) — deliberate behavior change, confirm in PR1 review |
| `ProductDownload` deleted on **every** product row (non-virtual re-import destroys virtual file association) | **Fix** — only touch when virtual columns are mapped |
| Inverted `regenerate` flag (ticking "Regenerate thumbnails" *disables* in-place regeneration in `copyImg`) | **Fix** (bug) |
| Images always converted to `.jpg` (legacy `copyImg`) | **Change** → the CQRS image pipeline keeps the original format — deliberate behavior change, confirm in PR1 review |
| Truncate option: wipes far more than the entity (carts refs, specific prices, **product image files on disk**, `ALTER TABLE … AUTO_INCREMENT` for categories); super-admin only under multistore; store contacts have no truncate branch | **Keep** semantics + gate; document destructiveness prominently in UI PR |
| `date_upd` forced to now on update | **Keep** |
| `shop` column semantics: shop id/name (products/categories/combinations), shop **group** (brands/suppliers), separate `id_shop` (customers); customer multi-shop fan-out (1 line → N rows) | **Keep**, document per importer (PR3) |
| Hook deferral (`Module::setBatchMode`) for products | **Not reproducible** via CQRS — hooks fire per command; benchmark in PR1 |
| Completion email (`sendemail` option) | **Keep** — dispatched when the run reaches `finished` (PR2/UI) |
| `ps_import_match` mapping presets | **Keep as-is** (BO-only helper, existing `ImportMatchRepository`, no CQRS — per #41907) |
| Required columns enforced ad hoc (products/categories have none at mapping level) | **Change** → `EntityField::isRequired` drives the validation phase |
| Separators truncated to one character; `skip` rows re-applied every batch | **Keep** (frozen on the run) |

## Pre-existing bugs found during the audit (independent of the refacto)

- `services/core/import.yml` wires entity 5 (Manufacturers/Brands) to the **supplier** fields provider; no `ManufacturerFieldsProvider` exists → fix with the Manufacturer importer (PR3).
- Brands lose `short_description` in the legacy controller (entity-index-vs-label truthiness bug in `AdminImportControllerCore`) → fixed by the Manufacturer importer (PR3).
- Category ntree never regenerated on the legacy non-AJAX path → fixed by the `finalization` phase (PR3).
- `CsvFileReader` uses deprecated `utf8_encode()`; legacy re-reads the whole file per batch for encoding checks → fixed by one-time normalization (PR1).
- Legacy Excel→CSV conversion forces `;` and caches by filename (stale reuse on re-upload) → fixed in normalization (PR1).

## Product import gaps (future evolution, not in this series)

Each gap = new fields on `ProductImporter::getFields()` + mapping to **existing** commands — no engine change needed:
specific-price tiers/currencies/groups (`AddSpecificPriceCommand`), pack contents (`SetPackProductsCommand`), customization field definitions (`SetProductCustomizationFieldsCommand`), multi-supplier (`UpdateProductSuppliersCommand`), attachments (`SetAssociatedProductAttachmentsCommand`), carriers (`SetCarriersCommand`), redirect settings (`UpdateProductCommand`), combinations in the product file / per-combination stock (`UpdateCombinationCommand`, `UpdateCombinationStockAvailableCommand`).

## Open items

- Constant values: sanity cap (proposed 10 000), GC retention (proposed 7 days). Message cap: **decided — 1 000 per severity**.
- Hook-per-command benchmark (PR1, measured 2026-08-03): **1 000 rows in 30.4 s ≈ 33 rows/s** (5 mapped columns ≈ 3 commands/row, `IMPORT_BENCHMARK=1 phpunit --filter ProductImporterBenchmarkTest`, local macOS dev box). Whether a mitigation (grouped/bulk paths per the #41321 spike) is needed before GA is still open.
- Removal of the deprecated classes: next minor vs next major (BC promise check).

Resolved in review round 1 (2026-07-31): BO UX on validation errors → the pause model (decision 6); accessory clearing → `@clear@` marker; legacy int mapping → dropped (decision 14).

## Tooling & workflow

- Behat import suite: `./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import`
- Integration tests: `php vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter <ClassOrMethod>`
- Unit tests: `php vendor/bin/phpunit -c tests/Unit/phpunit.xml --filter <ClassOrMethod>`
- Coding style: `php vendor/bin/php-cs-fixer fix` · static analysis: `php vendor/bin/phpstan analyse`
- CQRS inventory (before mapping a field, check the command exists): `./bin/console prestashop:list:commands-and-queries` or [.ai/generated/cqrs.md](../../generated/cqrs.md)
- Manual behavior reference: the legacy Import page (**Advanced Parameters > Import**) on any local shop
- Branch discipline: PR1 branches from `develop`; branch `41907-import-domain-layer` (PR #41911) is frozen until PR2.

## References

- Epic refinement: issue [#41907](https://github.com/PrestaShop/PrestaShop/issues/41907) (the issue body carries the earlier state-model refinement this plan supersedes where they differ — this file wins)
- Baseline PR: [#41911](https://github.com/PrestaShop/PrestaShop/pull/41911) (held, becomes PR2)
- Spike: [#41321](https://github.com/PrestaShop/PrestaShop/issues/41321) · CSV→CQRS normalization idea: [#42073](https://github.com/PrestaShop/PrestaShop/issues/42073) (superseded by PR1's importer design) · Playwright: [#41922](https://github.com/PrestaShop/PrestaShop/issues/41922)
- BC promise: [ADR 0017](https://github.com/PrestaShop/adr/blob/master/0017-backward-compatibility-promise.md)
