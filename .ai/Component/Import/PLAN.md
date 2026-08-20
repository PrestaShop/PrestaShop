# Import migration — architecture spec & roadmap

> **Temporary working document** for the #41907 refactoring. It is the source of truth for the PR series below; update the checklists as PRs land. **Delete this file when the migration reaches GA** — durable knowledge belongs in [CONTEXT.md](CONTEXT.md).
>
> **Audience: implementation sessions with no memory of the design conversation.** Together with [CONTEXT.md](CONTEXT.md), this file must contain everything needed to start PR1/PR2/PR3 from scratch — codebase map, baseline branch state, contracts, per-field decisions. If you change the architecture while implementing, update this file in the same PR.
>
> Last updated: 2026-08-19 (PR1 review round 6 applied: decision 23 — the finder/resolver contract, i.e. per-entity match-only finders vs resolve-or-create resolvers returning pure data, callers owning severity and wording, the identity exceptions removed. Round 5: decision 22 — lookup ambiguity policy, i.e. every name/reference lookup returns ALL matches, links warn and identity fails; re-import idempotency for specific prices and virtual files; `FileDownloader` confined to the content directories; soft-delete handling generalized in decision 16. Round 4 — QueryBuilder + `ShopConstraintTrait` refinements and identity-VO validation constants — was folded into decision 16 without a header bump).

## Decision log

1. **Abandon the old orchestration** (`Core\Import\Importer`, `Configuration\ImportRuntimeConfig`, `Handler\*`, `Adapter\Import\Handler\*`, session-coupled `@=` wiring). It was never switched on (step-1 forwards to legacy), covers 2/9 entities as ObjectModel ports, and entangles validation with insertion. Mark `@deprecated` in PR1; removal timing decided later (BC promise likely forces next major).
2. **Salvage the clean pieces**: `Core\Import\EntityField/**` **value objects** (`EntityField`, collection — the 8 provider *services* are deprecated once importers embed their field lists), `File/**` (readers, uploader, Excel→CSV), exception hierarchy, `Adapter\Import\{ImportDataFormatter, ImportEntityDeleter}`. `Core\Import\Configuration\**` (`ImportConfig` + `ImportRuntimeConfig` + their interfaces and factories) is fully deprecated — `ImportRunContext` is the single runtime object (see contract), built from the Doctrine entity, no factories. `ImageCopier` is replaced by a small engine `FileDownloader` (URL/path → local temp file); image association + thumbnail generation belong to the CQRS image commands.
3. **New engine namespace**: `Core\Import\Engine\` (sibling of the salvaged pieces).
4. **Hybrid persistence**: importers dispatch existing CQRS commands; a narrow import-specific repository covers only the gaps — forced-ID creation (*force IDs* option) and `date_add`. No ObjectModel → importers live in `Core`.
5. **Invalid rows: store-and-skip, always.** Validation scans the whole file; invalid row indexes are stored sparse per phase; later phases skip them; the run finishes even with errors. Internal sanity cap (~10 000 invalid rows): beyond it the run fails ("file appears malformed"). Persisted state is bounded by constants, never file size — no per-row table.
6. **Single run with pausing phases** (supersedes the earlier two-run flow — validation would have executed twice). A phase may be flagged *pausing* (validation is; future phases may be too, e.g. an association pre-validation): when it completes **with any message** (warning, or error meaning rows that will be skipped), the run stops as `awaiting_confirmation` so the BO modal / API client can review; a clean pausing phase continues in the same run without pause (legacy-like: no click for a clean file). Re-calling `RunImportBatch` on an `awaiting_confirmation` run **implicitly accepts** the messages and advances to the next phase; `CancelImportRun` rejects. Accepted caveat: a naive polling client auto-confirms. `options.dryRun` (engine-level, unused by the BO) truncates the phase list after `validation` for API validate-only — needed because clean files never pause. A dry run with errors still ends `finished` (reporting was its job); a full run fails only on sanity-cap breach or fatal error.
7. **Association existence checked twice, both times phase-based** (rewritten in review round 3 — supersedes the earlier end-of-validation in-memory sub-step): (a) a dedicated pausing `association_validation` phase between `database` and `association` — every file row exists in the DB by then, so each batch runs pure per-row DB probes through the same decision point as the association phase (stateless, cursor-resumable, no identity set, no memory bound; the `AccessoriesPrecheck` class and the `skipAssociationPrecheck` option are deleted); (b) defensive re-check at `association`-phase resolution (errors, run still completes). A cheap file-level warning stays at validation start when `accessories` is mapped without any owner column.
8. **Existence-failure severity is per-field policy** owned by each importer (auto-create vs warn-and-drop vs error) — see behavior inventory.
9. **Cleanup & GC**: terminal status clears `skipped_rows`, `shared_data`, `resume_cursor` and deletes the working file; capped `messages` kept for the post-run report. GC of old run rows, two mechanisms (core has no cron): (a) opportunistic purge in `StartImportRunHandler` — one indexed `DELETE` of terminal runs older than the retention constant, plus their leftover working files; (b) a `prestashop:import:purge-runs` console command for ops/real cron.
10. **PR #41911 held open**, reworked as PR2 once the engine (PR1) is merged.
11. **One state shape**: `RunImportBatchCommand` returns the same `ImportRunState` DTO as `GetImportRunState` — both are "the up-to-date state of the run"; `ImportBatchReport` is dropped.
12. **No session between wizard steps**: step-1 POSTs to step-2, the config travels as hidden inputs in the mapping form, and the final POST dispatches `StartImportRun` with everything (the file itself is already in the import directory from the upload AJAX). Supersedes the issue's "session kept pre-start" statement.
13. **Row position + opaque resume cursor**: the engine tracks progress in row positions (format-agnostic); after each batch the reader returns an opaque cursor persisted on the run (`resume_cursor`) — the CSV reader's cursor is a byte offset for `fseek`, a future JSON reader defines its own (split files, item index). The engine never interprets it. `CsvFileReader` is refactored directly with an additive resumable interface — no wrapper class.
14. **String entity ids end-to-end**: the legacy `Core\Import\Entity::TYPE_*` int mapping is dropped — the ints only feed deprecated code (step-1 form choices, session config, int-keyed provider finder, current `EntityType` VO), `ps_import_match` presets don't store an entity, and the feature flag being off means no production rows exist. `entity_type` column becomes a string in PR2.
15. **Canonical working-file dialect** (PR1 review): the normalizer rewrites EVERY input (CSV with the user's separator, or spreadsheet) into one canonical CSV dialect (`;`, `"`, empty escape, UTF-8, no BOM, blank lines preserved for row-index parity). The user-chosen separator is consumed once at normalization; the engine reader takes no per-run dialect (kills the session-coupled ctor wiring), `readFrom()` needs no dialect params.
16. **Lookups & fallback writes live on the existing repositories, EXISTING METHODS FIRST** (PR1 review round 2 + follow-up): the engine has NO dedicated lookup layer. Importer internals inject the existing repositories and MUST use a method that already serves the purpose, even VO-based/throwing ones (`StockAvailableRepository::getForProduct()` for the current quantity, Core `LanguageRepositoryInterface` `getOneByIsoCode()`/`findAll()` for language ids). **Deliberate carve-out (round 3): plain EXISTENCE checks go through the shared generic `ImportEntityExistenceChecker::exists(table, id)`** — import only ever needs a bool, so the earlier per-entity wrappers around `assert*Exists` added nothing but ctor dependencies and near-identical methods; one memoized table+id probe serves every importer (positive results only — the import creates entities mid-run), and tables with richer semantics are special-cased inside `probe()` (the `SOFT_DELETE_TABLES` const — `tax_rules_group` and, since round 5, `shop` — treats soft-deleted rows as absent, since assigning one would resurrect it; only the tables an importer actually probes are listed, so `carrier`/`currency` join when their PR3 importers land). Listing-oriented methods do NOT count as equivalents when they would load whole tables for a single-id resolution (that ruled out `getFeaturesByLang()`/`getFeatureValuesByLang()`). New repository methods are added ONLY when no fitting equivalent exists — and then with plain `int`/`string` params (`ShopConstraint` VOs are fine): `ProductRepository::getProductIdByReference()`, `FeatureRepository::getFeatureIdByName()`, `FeatureValueRepository::getFeatureValueIdByValue()`/`getProductCustomFeatureValueTexts()` (+ the fallback writes `createWithForcedId()`/`setDateAdd()`, hard PHPDoc warnings: import-engine use only), `CategoryRepository::getChildCategoryIdByName()`, `ManufacturerRepository::getManufacturerIdByName()`, `SupplierRepository::getSupplierIdByName()`, `ShopRepository::getShopIdByName()`. The tax rate for de-taxing `price_tin` comes from `Adapter\Tax\TaxComputer` directly. Core→Adapter injection is accepted pragmatism here — importers already live at the boundary. **New queries use the DBAL QueryBuilder** (review round 3, whatever the host repository's historical raw-SQL style) with `setMaxResults(1)` on single-row lookups — the rule is now also in the root `.ai/CONTEXT.md` coding standards. **Round 5 exception**: the name/reference lookups listed below are deliberately MULTI-row (`fetchFirstColumn()`, no `setMaxResults`, `ORDER BY <pk> ASC`) so ambiguity can be detected and reported — see decision 22. They are therefore renamed to their plural forms: `getProductIdsByReference()`, `getChildCategoryIdsByName()`, `getManufacturerIdsByName()`, `getSupplierIdsByName()`, `getShopIdsByName()`, `getFeatureIdsByName()`, `getFeatureValueIdsByValue()`. The `setMaxResults(1)` rule still holds for genuine single-row reads. **Round 4 refinements**: shop restrictions go through the pre-existing `Core\Repository\ShopConstraintTrait::applyShopConstraint()`, now mounted on `AbstractMultiShopObjectModelRepository` (the private ProductRepository duplicate is gone). The trait is deliberately minimalist and convention-based (unqualified `id_shop`/`id_shop_group` columns) — association tables (`product_shop`, `category_lang`) carry no `id_shop_group` column, so a shopGroup-scoped constraint on these lookups fails with a SQL error; no import path builds one today, handle it if PR2 exposes group-scoped runs. `getChildCategoryIdByName()` takes the run's `ShopConstraint` (category_lang names are per shop — legacy scoped the lookup with `Shop::addSqlRestrictionOnLang`). The validator's format constants come from the product identity VOs (`Gtin`/`Upc`/`Isbn`/`Reference` public `VALID_PATTERN`+`MAX_LENGTH`) instead of local copies — which also made ISBN validation match the VO's full ISBN-10/13 format (it previously only checked length).
17. **Importer auto-tagging via the interface** (PR1 review): no per-implementation attribute — `PrestaShopExtension` calls `registerForAutoconfiguration(EntityImporterInterface::class)->addTag(...)` (`#[AutoconfigureTag]` on an interface is ignored by Symfony 6.4), so any autoconfigured service implementing the interface is collected, module services included (their definitions must set `autoconfigure: true`). Proven by the `demoentityimporter` module in the example-modules repository.
18. **Skip rows are consumed at normalization, and the record count is measured there too** (PR1 review round 2): like the CSV separator, the skip count is a property of the ORIGINAL upload — `CsvImportFileNormalizer` strips the N leading records (blank lines count; spreadsheets always go through an intermediate CSV pass) so the working file contains DATA RECORDS ONLY. The same pass counts the records (`NormalizedImportFile` result); the count travels in the run's frozen config (`ImportRunContext::getDataRecordCount()`, `ps_import_run.total_rows` in PR2), so NOTHING downstream ever re-reads the file just to count — plain EOL counting or `wc -l` were rejected (records can span physical lines; exec is often disabled). The reader interface therefore has no countRecords() at all. The engine, `ImportRunContext` (no `skipRows` anymore) and importers never see a skip count; row indexes everywhere are 0-based data-record indexes. Presentation keeps spreadsheet-line parity: the skip count stays in the run's frozen config (`ps_import_run.skip_rows`, PR2) and the presenter/report layer adds it back when displaying source-file line numbers.
19. **`AbstractEntityImporter` base class** (PR1 review round 2): recommended base for core and module importers, hosting the duplicated mechanics — `iterateBatch()` (cursor-resumable batch loop over mapped rows), `assertKnownPhase()`, `containsError()`, `batchCompletesPhase()`, `contextWithBatchApplied()`, `isEmptyMappedRow()` and a default `countPhaseUnits()` = working-file record count (override to skip phases). Concrete importers hand the reader + row mapper to the base constructor (exposed as protected fields) and implement their phase processing. Implementing `EntityImporterInterface` directly stays supported. Proven by both `ProductImporter` and the `demoentityimporter` module.
20. **Multishop model** (PR1 review round 3): `ImportRunContext` carries the run's frozen **`ShopConstraint`** (ctor param; the dead `csvSeparator` param was removed at the same time). `getShopId()` stays as a DERIVED helper for the few paths that genuinely need exactly one shop (stock read, forced-id creation) and throws `ImportEngineException` outside a single-shop constraint. Every engine configuration read goes through **`ShopConfigurationInterface`** with the explicit constraint (PS_HOME_CATEGORY, PS_USE_ECOTAX, PS_CURRENCY_DEFAULT, PS_SHOP_COUNTRY_ID/PS_COUNTRY_DEFAULT). `getProductIdByReference()` takes the constraint (match_ref lookup scoped to the run's shops); **a reference matching a product OUTSIDE the scope is a row ERROR — never a duplicate creation** (validator check + `ReferenceOutsideShopScopeException` defense in the resolver; round 5 applies the same reasoning to a reference matching SEVERAL IN-SCOPE products — see decision 22). Auto-created manufacturers/features get the constraint's shop list (`ShopRepository::getAssociatedShopIds()`); `setDateAdd()` scopes the `product_shop` update. **Scoped lookups must never lead to auto-creating a duplicate entity for another shop**: feature/manufacturer/supplier/shop NAME lookups stay deliberately GLOBAL, and when an existing feature is REUSED for a run whose shops it is not associated with, the `feature_shop` association is ENSURED via `EditFeatureCommand` (`SetProductFeatureValuesCommand` writes `feature_product` but never `feature_shop`, while every feature read INNER JOINs it — without the ensure the imported values would be invisible on the run's shops). The category path walk also stays global (legacy parity). No equivalent shop-association edit command exists for manufacturers — known limitation, revisit if a report comes in.
21. **`association_validation` phase, no `shared_data` ever** (PR1 review round 3, see decision 7): the accessories pre-check is a first-class pausing phase after `database`, per-batch and stateless. Consequence for PR2: the `ps_import_run.shared_data` column is likely droppable entirely — keep imports stateless; phases re-derive everything from file + DB. A dedicated pre-create phase for auto-created entities (categories/manufacturers/features) was considered and REJECTED: auto-creation is a per-row concern already handled with in-memory caches during the database phase, and association validation does not depend on it.
22. **Lookup ambiguity is reported, and its severity follows the blast radius** (PR1 review round 5): none of the columns the import resolves by (`manufacturer.name`, `supplier.name`, `shop.name`, `feature_lang.name`, `feature_value_lang.value`, sibling `category_lang.name`, `product.reference`) carries a DB unique constraint, so a lookup can match SEVERAL entities. Legacy resolved these with `Db::getValue()` and no `ORDER BY`, i.e. NON-DETERMINISTICALLY. Every lookup therefore returns **all** matching ids ordered by id ASC (no `setMaxResults` — see decision 16), and the caller decides:
    - **ambiguous LINK → WARNING, lowest id used.** Manufacturer, supplier, shop, feature, feature value, category path, accessory owner and accessory target. The link is recoverable and the message carries the match count (`... matches %count% ...; the first one (id %id%) was used.`). Since the resolvers' name caches are per run (quiet after the first resolution — decision 23), each warning is emitted once per run, not once per row.
    - **ambiguous IDENTITY → row ERROR.** A `match_ref` reference matching several in-scope products would UPDATE an arbitrary one of them and silently discard the others — destructive and unrecoverable, so the row fails. Same reasoning as the out-of-scope reference rule in decision 20. Raised by `ProductRowValidator` in the pausing `validation` phase (before any write) and defended in the database phase by an explicit check in `ProductRowImporter::importRow()` (round 6 removed the exception-based defense along with the exceptions themselves — see decision 23).
    `ProductFinder::findTarget()` reports the two ambiguity kinds through the per-match strategies of `EntityLookupResult` (round 7): a first match `MATCHED_BY_ID` with further `MATCHED_BY_REFERENCE` entries is the id/reference collision (id wins), while an all-`MATCHED_BY_REFERENCE` ambiguous result is a plain multi-product reference. The multiplicity is `count()` — results are DTOs rather than out-parameters, so public signatures stay reference-free.
23. **Finder/resolver contract** (PR1 review round 6): the lookup services are organized by the CREATE/NO-CREATE seam and live in shared namespaces so PR3 importers reuse them individually (CategoryImporter needs exactly the category path walk; Combination needs the product identity):
    - **`Engine\EntityImporter\Finder\`** — MATCH-ONLY services (`ProductFinder`, `SupplierFinder`, `ShopFinder`): never create, never build messages, never throw. ONE result type: `EntityLookupResult { matches: list<{id, matchedBy}>, forcedId, foundOutsideShopScope }` (round 7) — `matchedBy` is stored PER MATCH so one result can mix strategies (the accessory id/reference collision), `forcedId` is generic (legacy consults forceIDs for every entity type), `foundOutsideShopScope` generalizes the match_ref out-of-scope state to any shop-scoped lookup. Caller contract: check `isAmbiguous()`/`foundOutsideShopScope` BEFORE using `first()` as an update target (pinned by the duplicate-reference test).
    - **`Engine\EntityImporter\Resolver\`** — RESOLVE-OR-CREATE services (`ManufacturerResolver`, `CategoryResolver::resolveChild()` — one path level per call, the WALK lives in the caller so it owns the segment name for warnings —, `FeatureResolver::resolveFeature()/resolveFeatureValue()/resolveCustomValues()` — the Name:Value:Position[:Custom] PARSE is the import-file format, i.e. the caller's —, + the shared `RunShopIdsProvider` memo): `resolve*()` is justified precisely because they may create. ONE result type: `ResolvedEntity {id, wasCreated, matchCount}`. Their name caches are QUIET: creation/ambiguity info is returned once per run, so callers warn once per run, not once per row.
    - **Callers own ALL interpretation** — severity, wording, message fields (decision 22 proved severity is context-dependent). Numeric-id branches (match-only by nature: an unknown id never auto-creates) live in the callers via `ImportEntityExistenceChecker`. The former identity exceptions (`AmbiguousReferenceException`, `ReferenceOutsideShopScopeException`) are deleted: `findRowMatch()` reports the two no-go states as data and both the validator and the row importer turn them into row errors with the same translation keys.
    - `EntityMatch` and `ResolvedAssociation` (near-duplicate DTOs with messages baked in) are replaced by exactly TWO result types — `EntityLookupResult` and `ResolvedEntity` (round 7 folded the interim `ProductRowMatch`/`ProductTargetMatch`/`CategoryPathResolution`/`FeatureEntryResolution` into them); nothing under `Finder\`/`Resolver\` references `ImportMessage` or the translator.
    - **PR3 note — row-identity finders**: `forceIds` is a GENERIC run option (legacy consults it in the category, product, customer... imports) while `match_ref` is product-identity-only (product import, plus the combination import locating the OWNING product — the Combination importer therefore REUSES `ProductFinder`). Each PR3 importer implements its own `findRowMatch()`-style method with its own matching rules, all returning the shared `EntityLookupResult` — the round-6 question of generalizing a product row DTO dissolved in round 7, since there is no product-specific result type left.

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
| `ImageCopier` | `src/Adapter/Import/` | **deprecated** — replaced by engine `FileDownloader` (download only); association + thumbnails via CQRS image commands |
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
    public function getLabel(): string;                           // translated entity label for the BO dropdown ('Products')
    public function getFields(): EntityFieldCollectionInterface;  // fields embedded in the importer (EntityField VOs); old provider services deprecated
    /** @return list<ImportPhaseDefinition> ordered — ids are open strings; validation/database/association/finalization are conventions */
    public function getPhases(): array;
    /** computed ONCE at phase entry, stored on the context by the caller; 0 => phase skipped */
    public function countPhaseUnits(string $phaseId, ImportRunContext $context): int;
    /** process up to $limit units from the phase's stored position */
    public function processPhaseBatch(string $phaseId, ImportRunContext $context, int $limit): PhaseBatchResult;
}
```

- `ImportPhaseDefinition` — technical id + translatable label + `isPausing` flag (a pausing phase completing with any message stops the run as `awaiting_confirmation` — see decision 6). **Code-defined only, never persisted**; the DB stores phase-id strings. Ids are **open strings** — the `PHASE_*` constants only name the common four; importers may declare custom phases (e.g. a combinations `attribute_generation` pre-phase). A stored id no longer matching `getPhases()` (deploy changed the list mid-run) fails the run gracefully.
- `ImportRunContext` — **the single runtime/config object** (replaces the deprecated `ImportConfig`/`ImportRuntimeConfig` pair and their factories): a plain core object mirroring the `ImportRun` entity's structure without depending on Doctrine, built from the entity by the adapter. Carries the frozen config (WITHOUT the skip count or the CSV separator — both consumed at normalization, decisions 15/18), reader position (row + resume cursor), the current phase's total unit count (set once by `enterPhase(string $phaseId, int $totalUnits)` so importers never rescan the file per batch), skipped-row set, options, and the run's `ShopConstraint` (decision 20; `getShopId()` is a derived single-shop helper). Row indexes are 0-based data-record indexes; presenters add `skip_rows` back for source-file line numbers.
- `PhaseBatchResult` — processed unit count, structured messages, newly skipped row indexes, new resume cursor.
- `EntityImporterRegistry` — collects tagged importer services (tag applied via `registerForAutoconfiguration()`, decision 17); powers the BO entity dropdown, the mapping-page field list, and batch dispatch. Module-extensible.

### Proposed engine layout (PR1)

```
src/Core/Import/Engine/
├── EntityImporterInterface.php
├── EntityImporterRegistry.php            # tagged services 'core.import.entity_importer'
├── ImportPhaseDefinition.php             # PHASE_VALIDATION|PHASE_DATABASE|PHASE_ASSOCIATION|PHASE_FINALIZATION + label wording
├── ImportRunContext.php
├── PhaseBatchResult.php
├── ImportMessage.php                     # severity, phase, message (+ optional row, field)
├── Exception/                            # UnknownEntityTypeException, UnknownPhaseException, MalformedImportFileException, …
├── File/
│   └── CsvImportFileNormalizer.php       # UTF-8 + Excel→CSV → run-scoped working file
│       # (Core\Import\File\CsvFileReader implements the standalone ResumableFileReaderInterface — plain array records + countRecords())
├── FileDownloader.php                    # URL/path → local temp file in sys_get_temp_dir() (replaces ImageCopier; CQRS commands own association + thumbnails)
└── EntityImporter/
    ├── AbstractEntityImporter.php        # recommended base: iterateBatch, phase guard, default unit count (decision 19)
    ├── ProductImporter.php               # extends the base; dispatches commands via Core\CommandBus\CommandBusInterface
    ├── ImportEntityExistenceChecker.php  # generic memoized table+id existence probe shared by every importer (decision 16 carve-out)
    ├── RowMapper.php / LocalizedValueTrait.php   # shared by every importer
    ├── Finder/                                   # match-only lookups (decision 23): ProductFinder, SupplierFinder, ShopFinder + EntityLookupResult
    ├── Resolver/                                 # resolve-or-create (decision 23): ManufacturerResolver, CategoryResolver, FeatureResolver, RunShopIdsProvider + ResolvedEntity
    └── Product/…                         # row validator / row importer (product-specific orchestration; lookups live in Finder/ and Resolver/)
```

Core/Adapter split rule (amended per decision 16, round 2): importers stay in Core (they only talk to the command bus)... except for reads and the two fallback writes, which use the EXISTING `Adapter\*\Repository\*` classes directly — no engine-owned lookup layer. New plain-`int` lookup methods were added to those repositories; the fallback writer is `ProductRepository::createWithForcedId()` + `ProductRepository::setDateAdd()` (ObjectModel `force_id`).

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
- **Phase totals count data rows** — the working file only contains data records (skip rows stripped at normalization, decision 18) and the count is measured by that same pass (`ImportRunContext::getDataRecordCount()`), so the default `countPhaseUnits()` reads nothing; legacy's `total_rows` counted physical lines including headers; fix in PR2.

### File handling

- `StartImportRun` normalizes the upload **once** into a run-scoped working file: encoding → UTF-8 (replaces the deprecated `utf8_encode()` and the legacy whole-file `mb_check_encoding` on every batch), Excel → CSV (fixing the legacy converter's forced `;` and stale filename cache), BOM stripped. **The working file always uses the canonical CSV dialect** (`;`, `"`, empty escape — constants on `CsvImportFileNormalizer`; decision 15): the user's separator AND the skip count are consumed at normalization (decision 18) — the engine reader is dialect-free and the working file holds data records only; blank lines are preserved so record mapping stays 1:1 apart from the stripped head. The working file lives next to the upload in the import directory, named by run id (e.g. `<runId>.work.csv`), and is deleted at terminal state.
- Batches resume by **row position + opaque reader cursor** persisted on the run (`resume_cursor` column): the engine asks the reader to resume at row N and hands back the last cursor; the CSV reader's cursor is a byte offset (`fseek`, O(1) resume — total O(n) per phase, not O(n²)); a future JSON reader defines its own (split files at init, item index). The engine never interprets the cursor.
- Reader stays behind `FileReaderInterface`, extended in place with the resumable contract (additive — `CsvFileReader` refactored directly, no wrapper); a Symfony-Serializer-based reader (JSON, native multi-language) can be added later without touching importers.

### Association existence checks

- **`association_validation` phase** (pausing, between `database` and `association` — decision 21, supersedes the end-of-validation in-memory sub-step): every file row exists in the DB by then, so each batch probes its rows' accessory targets with plain per-row DB lookups through `ProductFinder::findTarget()` — the SAME decision point the association phase uses, so both always agree. Stateless, cursor-resumable, no identity set, no memory bound, no skip option. Misses/ambiguities are warnings (pausing → the run stops as `awaiting_confirmation` before any link is written). A cheap file-level warning stays at validation start when `accessories` is mapped without any owner column.
- **Association phase** re-resolves each target against the DB at write time (covers DB drift between the phases). Unresolved target → **error naming the association**, link dropped, run completes.

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

`shared_data` **likely droppable entirely** (decision 21 — imports stay stateless: no more `crossStepsVars` / accessory map / `cat_moved`, phases re-derive from file + DB; decide when writing the PR2 schema). `validate_only` column **dropped** — replaced by `options.dryRun` (phase-list truncation, decision 6).

### Known constraints (stated, accepted)

- **Hooks fire per CQRS command** (legacy defers them via `Module::setBatchMode` for products). Slower on big files — accepted for the iso-functional first pass; benchmark in PR1, mitigation (grouped/bulk paths per the #41321 spike) deferred.
- **No wrapping transaction per row**: a runtime command failure mid-row leaves a partially imported entity (row marked failed, remaining commands skipped, structured error). Legacy has no transactions either; validation phase makes this rare.
- **"Handlers never call handlers"**: the sequencer dispatching commands through the bus is a sanctioned composition service (nothing injects another handler).
- CQRS-based importers need no legacy web context → PR2 Behat can finally exercise batch execution headlessly (the blocker recorded in #41911).

## PR roadmap

### PR1 — engine + ProductImporter (from `develop`, no CQRS layer) — **done 2026-08-03**, review round 2 applied 2026-08-05, round 3 (multishop + phase redesign) 2026-08-10 (same PR as the docs, #42247)

- [x] Engine contracts: `EntityImporterInterface`, `ImportPhaseDefinition` (open string ids + `pausing` flag), `ImportRunContext` (single runtime object), `PhaseBatchResult`, `ImportMessage`, `EntityImporterRegistry` (+ tag via `registerForAutoconfiguration`, decision 17), engine exceptions
- [x] File normalization service (`CsvImportFileNormalizer`: UTF-8 + Excel→CSV → run-scoped working file, canonical dialect per decision 15) + cursor-resumable reading (`CsvFileReader` refactored in place, STANDALONE `ResumableFileReaderInterface` — plain `array<int,string>` records, CSV cursor = byte offset yielded as generator key; the record count is measured by the normalization pass itself and carried by the context, the reader never counts)
- [x] `ProductImporter` (`validation` / `database` / `association_validation` / `association`) — fields embedded via `getFields()` (no provider service), field→command mapping below
- [x] `FileDownloader` (URL/path → local temp file in `sys_get_temp_dir()`) — replaces `ImageCopier`, which gets `@deprecated`
- [x] Import-specific fallback writes folded into `Adapter ProductRepository` (`createWithForcedId`, `setDateAdd` — narrow, hard PHPDoc warnings)
- [x] Association pre-check as the pausing `association_validation` phase (decision 21 — replaced the round-1 in-memory sub-step and its `skipAssociationPrecheck` option in review round 3)
- [x] Multishop model (decision 20, review round 3): `ShopConstraint` on the context, `ShopConfigurationInterface` everywhere, scoped `match_ref` with the never-duplicate rule, `feature_shop` ensure-on-reuse, `association_validation` phase, QueryBuilder conversions, FileDownloader hardening (128 MB cap + local-path confinement to the shop dir/sys temp), registry duplicate-entity-type throw, logger on the row/association catch-alls
- [x] `@deprecated since 9.3` on the abandoned classes (lists in decisions 1–2) — 27 files from the first pass + the legacy reading layer added in review round 2 (`FileReaderInterface`, `CsvFileReader::read()`, `FileOpenerInterface`, `CsvFileOpener`, the whole `DataRow`/`DataCell` layer), all still wired; removal comes later
- [x] Integration tests (`tests/Integration/Core/Import/Engine/`, generic `AbstractImportEngineTestCase` + product-bound `AbstractProductImportEngineTestCase`) driving the importer through a mini-sequencer (batch limit 2 → cursor resume everywhere) with CSV fixtures (`tests/Resources/import/`): create (every mapped column asserted), update via `forceIDs` and `match_ref`, mutual accessories (A↔B in one file) + `@clear@` + numeric targets (id-wins/reference-fallback warnings), invalid rows skipped + reported (zero validation writes, message texts asserted), gtin-over-ean13, customization counts (+ explicit 0/0 removal), mutually-exclusive reductions, multilang single-language file, images from local fixture paths (+ positional `image_alt` holes), virtual product, features (+ custom-value language rule on update), multishop (scoped writes, out-of-scope `match_ref` error, `feature_shop` ensure), behavior fixes (numeric manufacturer/minimal_quantity/out_of_stock/low_stock_alert warnings, `shop` cell separator + existence), registry duplicate-type throw, FileDownloader confinement + size cap
- [x] Hook-per-command benchmark: `IMPORT_BENCHMARK=1 phpunit --filter ProductImporterBenchmarkTest`, two scenarios since round 3 (local macOS dev box, per-phase split in the output):
  - scalar (5 columns ≈ 3 commands/row): **1 000 rows in 30.4 s ≈ 33 rows/s** (2026-08-03) → **26.4 s ≈ 37.8 rows/s** (2026-08-10, post-round-3 — same ballpark, run-to-run variance dominates)
  - associations (7 columns: + category/manufacturer/features/accessories, first measured 2026-08-10): **1 000 rows in 58.4 s ≈ 17.1 rows/s** — the database phase dominates (55.1 s, more commands per row); the two accessories phases cost **1.2 s (association_validation) + 2.1 s (association)** total, ~6 % of the run
  - mitigation decision before GA still open
- [x] Review round 5 (2026-08-17): lookup ambiguity policy (decision 22 — all seven lookups return every match; links warn with the count, `match_ref` identity fails the row via `AmbiguousReferenceException` + a validator check); re-import idempotency for specific prices and virtual files; `FileDownloader` local paths confined to the CONTENT directories (`admin/import`, `upload/`, `img/`, `download/`, sys temp) instead of the whole shop root, which exposed `app/config/parameters.php` and `.env` as downloadable virtual product files — the directories are injected via the ctor and wired in `import_engine.yml` (`!php/const` + `%prestashop.admin_dir%`), keeping the engine class decoupled from the legacy constants; `SOFT_DELETE_TABLES` generalized to cover `shop` (probe + name lookup); `getRunShopIds()` and the shop-name lookup memoized; row-failure messages keep the raw exception text only for `DomainException`/`ImportEngineException` (DBAL and `TypeError` details stay in the log); `hasValue()` extracted in `ProductRowImporter` (deliberately NOT `!empty()`, which would reject a legitimate `"0"`). Integration tests: `parameters.php` rejection, specific-price and virtual-file re-import, duplicate-reference warn-vs-error, soft-deleted shop. The engine test harness now stages `{FIXTURE_DIR}` assets into a temp dir so fixtures exercise the real confinement rather than needing it widened.
- [x] Deferred out of round 5: spreadsheet normalization memory (a chunked `IReadFilter` cannot bound it in PhpSpreadsheet 1.30.5 — `Xlsx::load()` parses the sheet XML twice into SimpleXML and rebuilds the full `sharedStrings` array on every call, and `rangeToArrayYieldRows()` only exists from 2.x; the CSV path already streams and the spreadsheet path is byte-for-byte legacy parity) and per-row transactions (waiting on the dedicated transaction PR + ADR).
- [x] Review round 6 (2026-08-19): finder/resolver split (decision 23) — `ProductIdentityResolver` becomes the match-only `Finder\ProductFinder` (`findRowMatch()` returns `ProductRowMatch` with the two identity no-go states as data; `AmbiguousReferenceException` and `ReferenceOutsideShopScopeException` deleted, the validator's ordering constraint with them); the 13-dependency `ProductAssociationResolver` splits into `SupplierFinder`/`ShopFinder` (match-only) and `ManufacturerResolver`/`CategoryResolver`/`FeatureResolver` (+`RunShopIdsProvider`); all message building moves to `ProductRowImporter`; `EntityMatch`/`ResolvedAssociation` replaced by `EntityLookupResult`/`ProductRowMatch`/`ProductTargetMatch`/`ResolvedEntity` (+2 composites). Behavior-preserving: message texts byte-identical, translation keys net zero, integration suite unmodified.
- [x] Review round 7 (2026-08-19, same session): DTO consolidation — the six result structures become TWO (`EntityLookupResult` with per-match `matchedBy` + generic `forcedId` + `foundOutsideShopScope`; `ResolvedEntity`); the category path WALK and the feature entry PARSE move into `ProductRowImporter`; `CategoryResolver::resolvePath()` becomes the more reusable `resolveChild()` (cache keyed `parent:name`, `PS_HOME_CATEGORY` read moves to the caller). Behavior-preserving, message texts byte-identical, integration suite unmodified.
- [x] Keep `.ai/Component/Import/` docs in sync

PR1 field-decision notes (resolved 2026-08-03, details in the mapping table / behavior inventory):
- `quantity` → read current stock (`StockAvailableRepository::getQuantityForProduct`), dispatch `setDeltaQuantity(target − current)`, skip when 0 (the stock command is delta-only by design).
- `supplier` → **no auto-create** (warn-and-drop): `AddSupplierCommand` requires an address the file cannot provide; existing suppliers still resolve by id/name.
- `customizable`/`uploadable_files`/`text_fields` → `uploadable_files`/`text_fields` are integer COUNTS (review round 2, not booleans): N FILE + M TEXT fields with numbered generic labels via `SetProductCustomizationFieldsCommand`; explicit `0`/`0` on an update dispatches `RemoveAllCustomizationFieldsFromProductCommand`; empty/unmapped cells leave the product untouched; `customizable` alone → warning.
- `low_stock_alert` → follows the command coupling (alert = threshold ≠ 0); contradicting file value → warning.
- Multilang UPDATE writes the file's language only (legacy fill-empty-languages dropped); creation still duplicates into every language.
- `is_virtual` on update only converts TO virtual; an explicit 0 never converts back (protects existing types/downloads).
- `shop` column: `SetProductShopsCommand` with the run's shop guaranteed in the list (it holds the just-written data).
- Repository usage (decision 16): existing methods reused wherever an equivalent exists (`getForProduct` for stock, Core `LanguageRepositoryInterface`; plain existence checks go through the generic `ImportEntityExistenceChecker` — see the decision 16 carve-out); only 8 genuinely new lookups added (`ProductRepository::getProductIdByReference` — ShopConstraint-scoped, `CategoryRepository::getChildCategoryIdByName`, `ManufacturerRepository::getManufacturerIdByName`, `SupplierRepository::getSupplierIdByName`, `ShopRepository::getShopIdByName`, `FeatureRepository::getFeatureIdByName`, `FeatureValueRepository::getFeatureValueIdByValue`, `FeatureValueRepository::getProductCustomFeatureValueTexts`) plus the two fallback writes on `ProductRepository`; all QueryBuilder-based with `setMaxResults(1)` on single-row lookups.
- Custom feature values on UPDATE (review round 3): `SetProductFeatureValuesCommand` REPLACES the custom value row (new row + orphan cleanup) and ObjectModel refills missing languages from the default one — so "write the file's language only" is implemented by reading the product's current custom texts (`getProductCustomFeatureValueTexts`) and re-sending them merged with the file language's new text.

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
- [ ] Row-identity finders per importer (`findRowMatch()`-style, returning the shared `EntityLookupResult`): only `forceIds` is shared; `match_ref` is product-identity-only and the Combination importer reuses `ProductFinder` for the owning product (decision 23, PR3 note)

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
| `gtin`, `ean13`, `isbn`, `upc`, `mpn` | database | `UpdateProductCommand` (details); `gtin` (67th field) wins over its legacy alias `ean13` when both are filled — both feed `setGtin()` |
| `manufacturer` | database | resolve by id/name, auto-create via `AddManufacturerCommand` (associated to the run's shops) → `UpdateProductCommand` (manufacturerId); an unknown NUMERIC value is warned and dropped — never a brand named "123" |
| `category` | database | resolve by id / name-path (`/` hierarchy), auto-create per missing path level via `AddCategoryCommand` → `SetAssociatedProductCategoriesCommand` (default = first entry) |
| `quantity`, `location`, `out_of_stock`, `minimal_quantity`, `low_stock_threshold`, `low_stock_alert` | database | `UpdateProductStockAvailableCommand` (location, out_of_stock; quantity converted to a delta from the current stock — the command is delta-only) / `UpdateProductCommand` (minimal_quantity, low_stock_threshold; alert derived = threshold ≠ 0, contradicting file value → warning) |
| `supplier`, `supplier_reference` | database | resolve by id/name — **no auto-create** (warn-and-drop; `AddSupplierCommand` needs address/city/country the file cannot provide) → `SetSuppliersCommand` + `UpdateProductSuppliersCommand` + `SetProductDefaultSupplierCommand` |
| `tags` | database | `SetProductTagsCommand` |
| `features` (`Name:Value:Position[:Custom]`) | database | resolve, auto-create via `AddFeatureCommand` (run's shops) / `AddFeatureValueCommand` → `SetProductFeatureValuesCommand`; reusing an existing feature ENSURES its `feature_shop` association covers the run's shops (decision 20); custom values follow the single-language-file rule (merge with current texts on update) |
| `image`, `image_alt`, `delete_existing_images` | database | `FileDownloader` (URL/path → temp file) → `AddProductImageCommand` / `UpdateProductImageCommand` (legend) / `DeleteProductImageCommand` |
| `is_virtual`, `file_url`, `nb_downloadable`, `date_expiration`, `nb_days_accessible` | database | `UpdateProductTypeCommand` (virtual) + `AddVirtualProductFileCommand`. **Round 5**: a product holds at most ONE virtual file, so re-import is an UPDATE — `VirtualProductFileRepository::findIdByProductId()` (new id-only variant, so `Core` never receives the legacy ObjectModel) then `UpdateVirtualProductFileCommand` (previously `ALREADY_HAS_A_FILE` failed the whole row) |
| `customizable`, `uploadable_files`, `text_fields` | database | `uploadable_files`/`text_fields` are integer COUNTS: N FILE + M TEXT numbered generic fields via `SetProductCustomizationFieldsCommand`; explicit `0`/`0` on update → `RemoveAllCustomizationFieldsFromProductCommand`; `customizable` alone → warning |
| `reduction_price`, `reduction_percent`, `reduction_from`, `reduction_to` | database | `AddSpecificPriceCommand` (legacy "basic reduction": single rule, all currencies/countries/groups, from qty 1); a row with BOTH kinds is ambiguous → warning + both dropped. **Round 5**: re-import is an UPDATE — `SpecificPriceRepository::findExisting()` first, then `EditSpecificPriceCommand` (previously the duplicate-rule rejection `NOT_UNIQUE_PER_PRODUCT` failed the whole row and got its accessories dropped). **Known limitation**: the lookup is keyed on the rule's dates, so a row that only changes `reduction_from`/`reduction_to` adds a SECOND rule — defining what identifies "the import's basic reduction" independently of its dates is deferred |
| `shop` | database | shop ids or names, split on the run's multiple-value separator, existence-checked (unknown id OR name → warning + drop) → `SetProductShopsCommand` / the run's `ShopConstraint` on commands |
| `date_add` | database | fallback repository (not expressible via commands; `date_upd` always forced to now, per legacy) |
| `accessories` | **association_validation** (pausing pre-check) then **association** | both phases share `ProductFinder::findTarget()` → `SetRelatedProductsCommand`; numeric targets: id wins (warning when a reference also matches), reference fallback when no id matches (warned); unresolved = warning at pre-check, error + dropped link at write |

**Update-vs-create decision** (legacy `productImportOne` parity, per row): (1) `match_ref` on **and** `reference` matches an existing product (shop-scoped lookup) → update that product; (2) else `id` present **and** the product exists → update it; (3) else create — via the fallback repository with the forced id when `forceIDs` is on and `id` is present, via `AddProductCommand` otherwise. `date_upd` is always now; `date_add` only settable through the fallback repository.

## Behavior inventory — keep / change / fix

| Legacy behavior | Decision |
|---|---|
| Auto-create missing category (name/path), manufacturer, feature + feature value | **Keep** (iso-functional) |
| Auto-create missing supplier | **Change** → warn-and-drop (resolved in PR1: `AddSupplierCommand` requires address/city/country the file cannot provide; a bare legacy-style supplier row is not reachable through commands) |
| Unknown *numeric* category creates a stub **with that forced id** under Home | **Change** → error at validation (creating ids as a side effect is a trap) — confirm in PR1 review |
| Unknown accessory target silently dropped | **Change** → warning + drop (`association_validation` pausing phase + association-phase error) |
| Accessories cannot be cleared (delete only runs when row has ≥1) | **Change** → explicit clear marker: a cell containing exactly `@clear@` empties the association (special characters make real-data collision unlikely; convention for multi-value association fields) |
| Multilang: one language per file (`iso_lang`); first import duplicates the value into every language | **Keep** for creation (JSON reader later enables true multi-language) |
| Multilang on UPDATE: legacy `fillInfo` also wrote any language whose current value was empty | **Change** → updates write the file's language only (resolved in PR1: fill-if-empty would need a per-row read of current localized values). Custom feature values follow the same rule since round 3 (current texts merged — the Set command replaces the value row) |
| Numeric `manufacturer` cell falling through to the name branch (unknown id → brand named "123") | **Change** → warning + drop (round 3, aligned with the category id policy) |
| `out_of_stock`/integer cells read through a bare `(int)` cast (`"abc"` → valid enum 0) | **Fixed by design** — strict `ValueParser::parseInteger()`/`parseCount()`; count fields (`minimal_quantity`, `nb_downloadable`, `nb_days_accessible`) reject negatives with a warning |
| `image_alt` empty entries collapsed (a hole shifted the following alts onto the wrong image) | **Fixed by design** — positional split keeps empty entries (`ValueParser::splitPreservingEmpty()`) |
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
- Hook-per-command benchmark (`IMPORT_BENCHMARK=1 phpunit --filter ProductImporterBenchmarkTest`, local macOS dev box): scalar shape **≈ 33–38 rows/s** (2026-08-03 baseline confirmed post-round-3 on 2026-08-10); association-heavy shape (categories + brands + features + accessories) **≈ 17 rows/s**, dominated by the database phase's extra commands — the accessories phases themselves are ~6 % of the run. Whether a mitigation (grouped/bulk paths per the #41321 spike) is needed before GA is still open.
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
