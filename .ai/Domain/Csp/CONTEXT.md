# Csp Domain

## Purpose

Collects browser Content-Security-Policy violation reports, lets the merchant curate a per-shop allow-list from them, and emits the resulting policy header on the storefront. It does NOT ship a fixed hardening policy: the base policy is deliberately tight so the real footprint surfaces under report-only, and the merchant widens it from the log.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Csp/` — Commands, CommandHandler interfaces, ValueObjects (`CspDirective`, `CspSource`, `CspRuleId`, `CspLogStatusFilter`), Exceptions. No Query layer — reads go through the grid query builder and the entity repositories |
| Policy engine | `src/Core/Csp/` — `CspPolicy` (additive policy value object), `CspReportParser`, `CspReportNormalizer`, hook-dispatcher interface. Persistence-free, pure |
| Adapter | `src/Adapter/Csp/` — command handlers, `CspHeaderBuilder`, `CspPolicyProvider`, `CspViolationRecorder`, `CspFeatureChecker`, `CspConfiguration`, `CspHeaderSubscriber`, `CspPolicyHookDispatcher`, `CspRuleValidator` |
| Doctrine entities | `src/PrestaShopBundle/Entity/CspRule.php`, `CspLog.php` + repositories (`csp_rule`, `csp_log` tables). No legacy ObjectModel — integer ids throughout |
| Storefront emission | `classes/controller/FrontController.php` (`sendContentSecurityPolicyHeaders()` delegate), `src/Adapter/Csp/CspHeaderSubscriber.php` (FrontKernel); public collector `controllers/front/CspReportController.php` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/SecurityHeadersController.php`, `src/Core/Grid/Definition/Factory/CspLogGridDefinitionFactory.php`, `src/Core/Grid/Query/CspLogQueryBuilder.php`, grid row accessibility checkers |

## Non-obvious patterns

- **Storefront emission bypasses CQRS.** The hand-built front-office container has no command/query bus (see [CONTAINERS.md](../../CONTAINERS.md)), so the header is built by the context-free `CspHeaderBuilder` + `CspPolicyProvider` plain services. Two entry points call the same builder: the legacy `FrontController` delegate (default dispatch) and `CspHeaderSubscriber` (the `PS_FF_FRONT_CONTAINER_V2` FrontKernel). The caller passes the shop id, report endpoint and theme contributions in, so the builder reads no Context. Both wrap the build in try/catch — a DB error or a throwing module skips the header, never 500s a page.
- **The report collector is a legacy FO controller on purpose.** `CspReportController` is public and unauthenticated, works on both the default dispatch and the FrontKernel fallback, and keeps `report-uri` outside `/admin`. It bounds untrusted input (64 KB body cap, `CspReportParser::MAX_REPORTS` per request, per-shop row cap) and strips the query/fragment from the document URI (it can carry reset tokens / PII) before storing.
- **The policy is additive, merged from four sources** by `CspPolicyProvider`: the tight base, curated `csp_rule` rows, the active theme's `global_settings.csp`, and module contributions via the `actionCspPolicyModifier` hook. `CspPolicy` only exposes `addSource()` — no remove/replace — so no contributor can strip another's source, and invalid theme/module tokens are dropped (and logged), never fatal.
- **The base policy seeds `'self'` on every directive that falls back to `default-src`** (script-src, style-src, connect-src, …), because the moment a curated source creates an explicit directive the `default-src` fallback stops applying — without the seed, a single curated `connect-src https://api` would drop same-origin requests under enforcement. `base-uri`/`frame-ancestors`/`form-action` are pinned to `'self'` and `object-src` to `'none'` precisely because they do NOT fall back to `default-src`.
- **Granular directives are coarsened** (`CspDirective::coarsen()`): `script-src-elem/-attr` → `script-src`, `style-src-elem/-attr` → `style-src`, applied in `CspPolicy::addSource`, `AddCspRuleHandler`, and report recording. A granular directive overrides its parent without inheriting its sources, so storing one would cancel the base `'self'`.
- **The grid is log-driven.** `CspLogQueryBuilder` is `csp_log` LEFT JOIN `csp_rule` on (shop, directive, source); computed `is_allowed` / `is_not_allowed` / `is_weakening` columns drive the row actions and the bulk-revoke checkbox. A manually added rule was never reported, so `AddCspRuleHandler` seeds a placeholder `csp_log` row (`insertPlaceholderIfAbsent`) to keep it visible and revocable. "Clear log" (`deleteByShop`) and row-cap eviction (`deleteLeastReportedByShop`) both keep any row that backs an allow-list rule.
- **Everything is per-shop via `ShopConstraint`.** The grid resolves the constraint to shop ids and fails closed (`1 = 0`) when it cannot; allow/revoke handlers verify the target row's shop is within the caller's scope, so one shop can never curate another's policy through a crafted id.
- **Enforcement is guarded.** `CspFeatureChecker` (feature flag `csp` + `PS_CSP_ENABLED`) is shared by the header builder and collector so both no-op identically; `PS_CSP_REPORT_ONLY` defaults to true. `CspConfiguration` refuses the report-only→enforcing transition unless the shop has a baseline (a collected log row OR a curated rule), and refuses it entirely in an all-shops/group scope. Sources in `CspSource::WEAKENING_KEYWORDS` (`'unsafe-inline'`, `'unsafe-eval'`, …) are flagged in the grid and warned about on the page.

## Canonical examples

- `src/Core/Domain/Csp/Command/AllowCspSourceCommand.php` + `src/Adapter/Csp/CommandHandler/AllowCspSourceHandler.php` — promote a collected violation to an allow-list rule
- `src/Adapter/Csp/CspPolicyProvider.php` + `src/Core/Csp/CspPolicy.php` — the additive storefront read path (no CQRS)

## Related

- [CONTAINERS.md](../../CONTAINERS.md) — why storefront emission uses context-free services instead of the command bus
- [Security Domain](../Security/CONTEXT.md) — the CSP page lives under the same Advanced parameters > Security back-office section
- `tests/Integration/Behaviour/Features/Scenario/Csp/` — Behat behavior scenarios
