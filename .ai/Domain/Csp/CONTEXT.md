# Csp Domain

## Purpose

Collects browser Content-Security-Policy violation reports on two surfaces, the storefront (per shop) and the back office (global), lets the merchant curate an allow-list from them, and emits the resulting policy header on each surface. It does NOT ship a fixed hardening policy: the base policy is deliberately tight so the real footprint surfaces under report-only, and the merchant widens it from the log.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Csp/` — Commands, CommandHandler interfaces, ValueObjects (`CspContext`, `CspDirective`, `CspSource`, `CspRuleId`), Exceptions. No Query layer — reads go through the grid query builders and the entity repositories |
| Policy engine | `src/Core/Csp/` — `CspPolicy` (additive policy value object), `CspReportParser`, `CspReportNormalizer`, hook-dispatcher interface. Persistence-free, pure |
| Adapter | `src/Adapter/Csp/` — command handlers, `CspHeaderBuilder`, `CspPolicyProvider`, `CspViolationRecorder`, `CspFeatureChecker`, `CspConfiguration`, `CspHeaderSubscriber`, `CspPolicyHookDispatcher`, `CspRuleValidator` |
| Doctrine entities | `src/PrestaShopBundle/Entity/CspRule.php`, `CspLog.php` + repositories (`csp_rule`, `csp_log` tables). No legacy ObjectModel — integer ids throughout |
| Storefront emission | `classes/controller/FrontController.php` (`sendContentSecurityPolicyHeaders()` delegate), `src/Adapter/Csp/CspHeaderSubscriber.php` (FrontKernel); public collector `controllers/front/CspReportController.php` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/CspController.php` (CSP tab) + `SecurityHeadersController.php` (static-headers tab); two grids — violations (`CspLogGridDefinitionFactory` + `CspLogQueryBuilder`) and allow-list (`CspRuleGridDefinitionFactory` + `CspRuleQueryBuilder` + `CspRuleFilters`); grid row accessibility checkers; page JS `admin-dev/themes/new-theme/js/pages/csp/index.ts` |

## Non-obvious patterns

- **Storefront emission bypasses CQRS.** The hand-built front-office container has no command/query bus (see [CONTAINERS.md](../../CONTAINERS.md)), so the header is built by the context-free `CspHeaderBuilder` + `CspPolicyProvider` plain services. Two entry points call the same builder: the legacy `FrontController` delegate (default dispatch) and `CspHeaderSubscriber` (the `PS_FF_FRONT_CONTAINER_V2` FrontKernel). The caller passes the shop id, report endpoint and theme contributions in, so the builder reads no Context. Both wrap the build in try/catch — a DB error or a throwing module skips the header, never 500s a page.
- **The report collector is a legacy FO controller on purpose.** `CspReportController` is public and unauthenticated, works on both the default dispatch and the FrontKernel fallback, and keeps `report-uri` outside `/admin`. The whole body is attacker-controlled, so it bounds and validates untrusted input: POST only, 32 KB body cap, `CspReportParser::MAX_REPORTS` per request, directive enum + source-shape validation, and it **drops any report whose document-uri host is not one of the shop's own hosts** (`shopHosts()`) before storing. The document URI is query/fragment-stripped (it can carry reset tokens / PII), and one row is kept per source **per page**. It never reflects input and always answers 204; it reads no client IP (rate-limiting belongs at the edge).
- **The policy is additive, merged from four sources** by `CspPolicyProvider`: the tight base, curated `csp_rule` rows, the active theme's `global_settings.csp`, and module contributions via the `actionCspPolicyModifier` hook. `CspPolicy` only exposes `addSource()` — no remove/replace — so no contributor can strip another's source, and invalid theme/module tokens are dropped (and logged), never fatal.
- **The base policy seeds `'self'` on every directive that falls back to `default-src`** (script-src, style-src, connect-src, …), because the moment a curated source creates an explicit directive the `default-src` fallback stops applying — without the seed, a single curated `connect-src https://api` would drop same-origin requests under enforcement. `base-uri`/`frame-ancestors`/`form-action` are pinned to `'self'` and `object-src` to `'none'` precisely because they do NOT fall back to `default-src`.
- **Granular directives are coarsened** (`CspDirective::coarsen()`): `script-src-elem/-attr` → `script-src`, `style-src-elem/-attr` → `style-src`, applied in `CspPolicy::addSource`, `AddCspRuleHandler`, and report recording. A granular directive overrides its parent without inheriting its sources, so storing one would cancel the base `'self'`.
- **Two grids per surface, keyed on what you curate.** The violations grid (`CspLogQueryBuilder`) lists reported sources not yet allowed: it excludes any source already on the allow-list with a `NOT EXISTS` subquery against `csp_rule` (no LEFT JOIN, no `is_allowed`), and exposes a computed `is_weakening` and a `source_location` (source file + line) alongside the `sample`. The allow-list grid (`CspRuleQueryBuilder`) lists curated `csp_rule` rows with Remove / bulk Remove. "Allow" (`AllowCspSourceHandler`) promotes a `(directive, source)` to a rule, after which the `NOT EXISTS` filter hides every page's violation row for it (no placeholder rows — `AddCspRuleHandler` just inserts the rule). The log and the allow-list are separate tables: clearing or pruning the log never touches `csp_rule`, and pruning deletes freely (allowed sources are a different table, so they are never pruned). The back office additionally renders its built-in first-party sources read-only (`CspPolicyProvider::getAdminFirstPartySources()`).
- **Everything is per-shop via `ShopConstraint`.** The grid resolves the constraint to shop ids and fails closed (`1 = 0`) when it cannot; allow/revoke handlers verify the target row's shop is within the caller's scope, so one shop can never curate another's policy through a crafted id.
- **Enforcement is guarded.** `CspFeatureChecker` (feature flag `csp` + `PS_CSP_ENABLED`) is shared by the header builder and collector so both no-op identically; `PS_CSP_REPORT_ONLY` defaults to true. `CspConfiguration` refuses the report-only→enforcing transition unless the surface has a **curated allow-list rule** (a collected-but-unreviewed log row does not count, since enforcing on it would block every reported source), and refuses it entirely in an all-shops/group scope. Sources in `CspSource::WEAKENING_KEYWORDS` (`'unsafe-inline'`, `'unsafe-eval'`, …) are flagged in the grid and warned about on the page. Disabling the feature stops its background work too: `prestashop:csp:prune-log` skips any surface where CSP is off.

## Canonical examples

- `src/Core/Domain/Csp/Command/AllowCspSourceCommand.php` + `src/Adapter/Csp/CommandHandler/AllowCspSourceHandler.php` — promote a collected violation to an allow-list rule
- `src/Adapter/Csp/CspPolicyProvider.php` + `src/Core/Csp/CspPolicy.php` — the additive storefront read path (no CQRS)

## Related

- [CONTAINERS.md](../../CONTAINERS.md) — why storefront emission uses context-free services instead of the command bus
- [Security Domain](../Security/CONTEXT.md) — the CSP page lives under the same Advanced parameters > Security back-office section
- `tests/Integration/Behaviour/Features/Scenario/Csp/` — Behat behavior scenarios
