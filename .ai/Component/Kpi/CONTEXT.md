# Kpi Component

## Purpose

KPI tiles render a small numeric or short-text indicator above an admin listing (e.g. "Pending Discussion Threads", "Average Customer Age"). The component covers the shared infrastructure used by every admin page that displays such tiles, including the legacy `AdminStats` ajax endpoint that supplies the cached values.

## Layers

| Layer | Path |
|-------|------|
| Core interface | `src/Core/Kpi/KpiInterface.php` |
| Row aggregator | `src/Core/Kpi/Row/HookableKpiRowFactory.php` (groups N KPIs into a row, dispatches the hook used by modules to inject extra tiles) |
| Adapter — base class | `src/Adapter/Kpi/AbstractAdminStatsKpi.php` |
| Adapter — concrete KPIs | `src/Adapter/Kpi/{Subject}Kpi.php` (one class per tile) |
| Service registration | `src/PrestaShopBundle/Resources/config/services/adapter/kpi.yml` (per-KPI services) + `src/PrestaShopBundle/Resources/config/services/core/kpi.yml` (factories) |
| Twig rendering | `{{ render(controller('CommonController::renderKpiRowAction', {kpiRow: ...})) }}` |

## Non-obvious patterns

- **AbstractAdminStatsKpi is the canonical base** for any tile that reads its value from a `ConfigurationKPI::get(...)` cache populated by `AdminStatsController`. Concrete KPIs only declare id / icon / color / titles / configuration keys; `render()` lives in the base.
- **Always handle the `*_EXPIRE` cache key explicitly.** A missing entry returns `false`; coerced into `< time()` it evaluates true (since `false` casts to `0`), which silently means "refresh on every load". Use the explicit form `false === $expireTimestamp || (int) $expireTimestamp < time()` so the intent is obvious — the legacy KPIs all rely on this behaviour, but the explicit form documents it.
- **The hook in `HookableKpiRowFactory`** is what lets modules append extra KPI tiles to an existing row. When registering a new factory, name the hook segment after the page (`customer_service`, `orders`, `customers`); modules listen on the resulting `actionAdminCustomerServiceKpiRow` style hook.
- **Source URLs are computed by the legacy `AdminStats` controller.** Each KPI exposes a `source` link to `AdminStats?ajax=1&action=getKpi&kpi=...`; the front-office calls it asynchronously when `refresh` is true. Migrating off `AdminStats` is a separate, larger refactor — see [GOTCHAS.md](../../GOTCHAS.md) and the AdminCustomerThreads migration KPI refactor follow-up.

## Canonical examples

- `src/Adapter/Kpi/AbstractAdminStatsKpi.php` — base class
- `src/Adapter/Kpi/PendingDiscussionThreadsKpi.php` — minimal concrete KPI
- `src/PrestaShopBundle/Resources/config/services/core/kpi.yml` — factory wiring (the `prestashop.core.kpi_row.factory.{page}` pattern)
- `src/PrestaShopBundle/Controller/Admin/Sell/Customer/CustomerController.php` — controller injection + Twig render call

## Migrating a legacy `renderKpis()`

When migrating a legacy admin page that defines `renderKpis()`:

1. Identify the three or four `HelperKpi` instances in the legacy method. Each one becomes a class extending `AbstractAdminStatsKpi`.
2. Register each KPI as a service in `services/adapter/kpi.yml` with the existing `AdminStats?...&kpi=...` source URL — do **not** change the source URL; values must match the legacy page exactly.
3. Register a row factory `prestashop.core.kpi_row.factory.{page}` in `services/core/kpi.yml` aggregating the new KPIs.
4. Inject the factory in the migrated controller's listing action and pass `$factory->build()` to the template.
5. Render via `{{ render(controller('PrestaShopBundle\\Controller\\Admin\\CommonController::renderKpiRowAction', {kpiRow: ...})) }}`.

Refactoring the KPI value source itself (replacing `AdminStats` ajax with a CQRS query) is **out of scope** for an iso-functional migration. Track it as a follow-up issue and keep the legacy endpoints during the first migration PR.

## Related

- [Migration Component](../Migration/CONTEXT.md) — the migration orchestrator references this context when a page has KPIs
- [Hook Component](../Hook/CONTEXT.md) — `HookableKpiRowFactory` dispatches the hook used to extend rows
- [Twig Component](../Twig/CONTEXT.md) — `CommonController::renderKpiRowAction` is the canonical rendering entry point
