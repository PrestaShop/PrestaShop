# Migration Component

## Purpose

Infrastructure and skills for migrating PrestaShop legacy admin pages (ObjectModel + legacy controllers) to modern Symfony/CQRS architecture. Covers the full lifecycle: audit, domain layer, UI, testing, GA, and legacy deprecation.

## Layers

| Layer | Path |
|-------|------|
| Migration orchestrator skill | `.ai/Component/Migration/skills/legacy-to-symfony-migration/` |
| Audit skills | `audit-legacy-controller`, `audit-object-model`, `generate-migration-manifest` |
| Lifecycle skills | `promote-feature-flag-to-stable`, `write-upgrade-sql-script`, `create-removal-issue` |

## Reference pages by complexity

All fully migrated unless noted:

| Tier | Page | Fields | Grids | Tabs | JS pattern | Key specificity |
|------|------|--------|-------|------|------------|-----------------|
| Simple | Tax | 3 | 1 | No | `initComponents()` | Baseline CRUD + options panel |
| Simple | Contact | 5 | 1 | No | `initComponents()` | Translatable fields, multistore |
| Medium | Manufacturer | 8 | 2 | No | `initComponents()` | Sub-resource (addresses), image upload, export |
| Medium | Category | 13 | 1 | No | `initComponents()` | Position management, tree hierarchy, SEO |
| Medium | Employee | 12 | 1 | Yes | `initComponents()` | Profile-based permissions, password policy |
| Complex | Customer | 20+ | 7 | Yes | Vue | Multiple related grids, B2B fields |
| Complex | Order | view-only | 5+ | Yes | Vue | 48 actions, sub-resources (shipments, payments) |
| Complex | Carrier | many | 1 | Yes | Vue | Multi-tab form, sub-resource ranges |

> **Note:** Vue.js is the exception, not the default. Most pages use `initComponents()` with standard PS JS components. Only use Vue for complex UX synchronization.

## Key PS-specific rules (always apply during migration)

- Multistore has three tiers: (1) no shop relation, (2) simple shop association, (3) per-shop content. `AbstractMultiShopObjectModelRepository` is required for tier 3, useful for tier 2, not needed for tier 1
- Sub-resources with their own table get their own Command when the domain is non-trivial; for simple cases editing them inside `EditXxxCommand` is acceptable
- Feature flag is the routing mechanism that toggles between legacy and Symfony pages: routes under migration carry `_legacy_feature_flag`. Once the page is GA and the legacy controller is removed (next major), the flag attribute is no longer needed
- Legacy controller stays during a deprecation period (typically 1–2 minor releases once the migrated page is GA and stable), then is removed in the next major version. A deprecation banner inside the legacy page is an exceptional pattern, not a default step
- `IdentifiableObject` DataProvider + DataHandler pattern replaces Symfony form events
- `NavigationTabType` for multi-tab forms — not standard Symfony tabs (exception, not default — most pages do not use tabs)
- File uploaders are a domain interface (in `src/Core/Domain/`), implemented in Adapter
- Listing and form migrations can be separate milestones, often months or years apart
- `stability="beta"` to `"stable"` is a formal step requiring its own PR
- Vue components are only needed when a form field is too dynamic for standard JS components — most pages use `initComponents()`

## Clean-break rules (don't carry legacy shape into the migrated code)

The migration is the occasion to do things cleanly — replicating the legacy structure is a common mistake:

- **No legacy context.** Don't inject values read from the legacy `Context` (e.g. `Context::getContext()->language->id`). Use the modern context services — `LanguageContext`, `ShopContext`, etc. — passed where needed. This applies to query builders, providers, and services alike
- **One URL = one action.** Don't keep the legacy controller's habit of a single endpoint that branches on a `method`/`action` parameter to do create *and* delete. Migrated controllers get distinct actions + routes per operation
- **Generic logic goes in a generic Core component, not a domain namespace.** If a helper has no business logic specific to the domain being migrated (e.g. filling localized names from defaults), put it under the matching `PrestaShop\PrestaShop\Core\{Concern}` namespace so other domains can reuse it — not under `Core\Domain\{ThisDomain}\`
- **Keep shared-component changes out of scope.** Don't modify shared JS components (`modal.ts`, `confirm-modal.ts`, `Link.php`, generic legacy list behavior) to make one page work — it risks side effects on every other consumer. If a shared component genuinely needs a change, do it in a dedicated PR; otherwise adapt at the call site
- **Don't replicate legacy bugs as features.** When the legacy page did something dubious, flag it rather than faithfully porting it; a pre-existing legacy-wide bug is a separate dedicated fix, not part of the migration

## Lifecycle rules

### GA (promote to stable)

- Triggered when the migrated page has been stable for at least one minor release with no P1 regressions — timing is per-page judgment, not a fixed duration
- Upgrade SQL (in the `autoupgrade` module) must be **idempotent** when needed — safe to run multiple times
- Release notes for the version document the new page as stable

### Removal (next major version)

- Typically waits 1–2 minor releases after GA so module developers can adapt — exact horizon is per-release planning
- Removal issue must reference the GA PR by number
- A deprecation banner inside the legacy controller (`$this->warnings[]`) is an exceptional pattern that has rarely been applied; do not add one as a default step
- Never use `@trigger_error()` from the legacy controller — it causes log noise merchants cannot fix

## Dependency graph

```
Audit (audit-legacy-controller, audit-object-model, generate-migration-manifest)
                                    |
                                    v
Feature Flag (register-feature-flag)
  must exist before any conditional CQRS code; latest acceptable: controller wiring
                                    |
                                    v
Domain layer (create-cqrs-commands, create-cqrs-queries)
                                    |
                                    v
Adapter layer (create-doctrine-repository, implement-cqrs-handlers, register-cqrs-services)
                                    |
                                    v
Behat tests (create-behat-context, write-behat-scenarios) — GATE: all green
                                    |
                                    v
╔══════════════════════════════════════════════════════════════════════════╗
║   VERTICAL SLICES — order interchangeable; listing-first is typical       ║
║   First slice creates controller class + routing file. Second extends.    ║
╠══════════════════════════════════════════════════════════════════════════╣
║   Listing slice                       Form slice                           ║
║   ─────────────                       ──────────                           ║
║   create-grid-definition              CRUD: create-crud-form-type          ║
║   create-grid-query-builder                 create-form-tab-layout (cond.) ║
║   create-position-column (cond.)            create-crud-form-data-handling ║
║   create-controller-listing           Settings: create-settings-form       ║
║   create-admin-routing (listing)      create-controller-form-actions       ║
║   create-twig-index-template          create-admin-routing (form)          ║
║   create-ts-entry-point (listing)     create-twig-form-template            ║
║   init-grid-extensions                create-ts-entry-point (form)         ║
║                                       init-js-components                   ║
║                                       create-vue-component (exception)     ║
╚══════════════════════════════════════════════════════════════════════════╝
                                    |
                                    v
Playwright tests (create-playwright-page-objects, create-playwright-test-data,
                  write-playwright-campaigns)
                                    |
                                    v
GA (promote-feature-flag-to-stable, write-upgrade-sql-script — conditional)
                                    |
                                    v
Removal (next major: create-removal-issue)
```

## Conditional activation

| Condition (from audit) | What it activates |
|---|---|
| Has enum-like fields | Semantic value objects |
| Has sub-resources with own table | Sub-resource commands, repositories, handlers, behat scenarios |
| Has computed/image columns in grid | GridDataFactory decorator |
| Has multistore (tier 2/3) | Multistore repository pattern, multistore behat scenarios |
| Has file uploads | File uploader interface + implementation |
| Has i18n fields | TranslatableType fields, i18n behat scenarios |
| Has `position` column | PositionColumn, position grid extension, position Playwright campaign |
| Has dynamic form fields (Vue needed) | Vue component, form manager, webpack entry |
| Has upgrade path for existing installs | Upgrade SQL |

## Inter-skill communication contracts

Key artifacts that cross skill boundaries:

| From | To | Contract |
|---|---|---|
| Audit manifest | All downstream skills | Entity name, table, field definitions, action list |
| Domain layer | Adapter layer | Interface FQCNs, exception class names, DTO getter list |
| Domain exceptions | Behat tests | Exception class names for error scenario assertions |
| DTO getters | Form DataProvider | `getData()` array keys match DTO structure |
| Command signatures | Form DataHandler | `create()`/`update()` dispatch commands using form data |
| Grid definition (GRID_ID) | Controller + JS | Grid factory service ID, `{Domain}Filters` class, JS Grid constructor ID |
| Form services | Controller | FormBuilder + FormHandler service IDs |
| Route names | Twig templates + JS | `path()` calls, grid action URLs |
| Feature flag name | Playwright tests + routing | Flag matching between XML, routing YAML, and test setup |
| Webpack bundle name | Twig template | `<script>` asset reference |

## Migration timing in practice

Migrations can span multiple PRs over months or years (listing first, form later, sub-resources later still). Decide milestone strategy explicitly during the audit (`generate-migration-manifest`). Carrier is **not** the reference example — it accumulated several exceptions (tab navigation, Vue ranges, deprecation banner) that should not be promoted as defaults.

## Skills

| Skill | Trigger |
|-------|---------|
| [`legacy-to-symfony-migration`](skills/legacy-to-symfony-migration/SKILL.md) | "migrate the Xxx admin page" |
| [`audit-legacy-controller`](skills/audit-legacy-controller/SKILL.md) | "audit AdminXxxController" |
| [`audit-object-model`](skills/audit-object-model/SKILL.md) | "audit Xxx ObjectModel" |
| [`generate-migration-manifest`](skills/generate-migration-manifest/SKILL.md) | "generate migration manifest" |
| [`promote-feature-flag-to-stable`](skills/promote-feature-flag-to-stable/SKILL.md) | "promote {Domain} to GA" |
| [`write-upgrade-sql-script`](skills/write-upgrade-sql-script/SKILL.md) | "write upgrade SQL script" (autoupgrade module) |
| [`create-removal-issue`](skills/create-removal-issue/SKILL.md) | "create removal issue for AdminXxx" |

## Related

- [CQRS Component](../CQRS/CONTEXT.md) — commands, queries, handlers
- [Controller Component](../Controller/CONTEXT.md) — Symfony admin controllers
- [Forms Component](../Forms/CONTEXT.md) — form types, DataProvider/DataHandler
- [Grid Component](../Grid/CONTEXT.md) — grid definitions, query builders
- [Behat Component](../Behat/CONTEXT.md) — integration tests
- [Playwright Component](../Playwright/CONTEXT.md) — UI tests
- [Javascript Component](../Javascript/CONTEXT.md) — JS entry points, components, grid extensions
- [Twig Component](../Twig/CONTEXT.md) — admin templates
