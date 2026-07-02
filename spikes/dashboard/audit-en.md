# Spike — Dashboard PrestaShop (#41522)

> **Issue:** https://github.com/PrestaShop/PrestaShop/issues/41522

---

## 1. Context

The admin Dashboard is **100% legacy**: no Symfony migration has been started. This spike maps everything that makes up the page, identifies blockers, and evaluates migration feasibility.

---

## 2. Controller

| File | Type |
|------|------|
| `controllers/admin/AdminDashboardController.php` | Legacy (`AdminController`) |

**Key points:**
- Legacy routing via `PrestaShopBundle\Controller\Admin\LegacyController::legacyPageAction`
- AJAX actions in the controller:
  - `ajaxProcessRefreshDashboard` — widget data
  - `ajaxProcessSetSimulationMode` — demo mode toggle
  - `ajaxProcessSaveDashConfig` — widget config save
- Config: `PS_DASHBOARD_SIMULATION`, per-employee dates (`stats_date_from`, `stats_date_to`, etc.)

---

## 3. Dispatched Hooks

| Hook | Trigger | Parameters |
|------|---------|------------|
| `dashboardZoneOne` | `renderView()` | `date_from`, `date_to` |
| `dashboardZoneTwo` | `renderView()` | `date_from`, `date_to` |
| `dashboardZoneThree` | `renderView()` | `date_from`, `date_to` (optional zone) |
| `dashboardData` | `ajaxProcessRefreshDashboard()` | `date_from`, `date_to`, `compare_from`, `compare_to`, `extra` |
| `displayDashboardToolbarTopMenu` | `page_header_toolbar.tpl` | — |
| `displayDashboardTop` | `page_header_toolbar.tpl` | — |
| `displayDashboardToolbarIcons` | Declared but **never called** in templates | — |
| `actionAdminControllerSetMedia` | Used by modules to inject their assets | — |

---

## 4. Templates

| File | Role |
|------|------|
| `admin-dev/themes/default/template/controllers/dashboard/helpers/view/view.tpl` | Main view (Smarty, default theme) |
| `admin-dev/themes/default/template/helpers/calendar/calendar.tpl` | Calendar widget (rendered by `HelperCalendar`) |
| `admin-dev/themes/default/template/page_header_toolbar.tpl` | Toolbar hooks |

The main view is in the **default theme**, not the new-theme. Bootstrap 3-column layout: Zone One `col-lg-3`, Zone Two `col-lg-7/9`, Zone Three `col-lg-2` (empty natively).

---

## 5. JavaScript

| File | Nature |
|------|--------|
| `js/admin/dashboard.js` | Dashboard core — AJAX, widget rendering, config |
| `js/vendor/d3.v3.min.js` | D3.js **v3** (obsolete, ~2016) |
| `admin-dev/themes/{theme}/js/vendor/nv.d3.min.js` | NVD3 — charts |
| `admin-dev/themes/{theme}/js/date-range-picker.js` | Calendar (loaded by `HelperCalendar`) |
| `admin-dev/themes/{theme}/js/calendar.js` | Calendar (loaded by `HelperCalendar`) |

No webpack entry point — everything loaded via legacy `addJS()`. No Dashboard assets in the new-theme.

---

## 6. Native Modules

| Module | Zone | Hooks | Own JS |
|--------|------|-------|--------|
| `dashactivity` | ZoneOne | `dashboardZoneOne`, `dashboardData`, `actionAdminControllerSetMedia` | `dashactivity.js` (NVD3 pie chart) |
| `dashtrends` | ZoneTwo | `dashboardZoneTwo`, `dashboardData`, `actionAdminControllerSetMedia` | `dashtrends.js` (NVD3 line chart) |
| `dashgoals` | ZoneTwo | `dashboardZoneTwo`, `dashboardData`, `actionAdminControllerSetMedia` | `dashgoals.js` (NVD3 bar chart) |
| `dashproducts` | ZoneTwo | `dashboardZoneTwo`, `dashboardData` | none |

Common pattern:
- `hookActionAdminControllerSetMedia`: checks `get_class($this->context->controller) == 'AdminDashboardController'` to load JS
- `hookDashboardZoneX`: assigns Smarty variables + calls `$this->display()` → Smarty
- `hookDashboardData`: returns a PHP array interpreted by `dashboard.js` via AJAX
- `dashactivity::hookDashboardZoneOne` calls `renderConfigForm()` → **`HelperForm`** → Smarty

---

## 7. Impact Analysis

| Blocker | Severity | Detail |
|---------|----------|--------|
| **Modules use `HelperForm`/`HelperCalendar` in their hooks** | Critical | These helpers render Smarty templates (`helpers/form/form.tpl`, `helpers/calendar/calendar.tpl`) requiring admin Smarty template dirs to be initialized — normally done by `AdminController::init()` |
| **`get_class($controller) == 'AdminDashboardController'` coupling** | High | All 4 modules check the exact class name to load their JS |
| **Hook templates in Smarty** | High | `dashboard_zone_one.tpl`, `dashboard_zone_two.tpl` etc. are Smarty templates inside modules |
| **`dashboard.js` not webpack-bundled** | High | Legacy JS file without a module system — must be ported or kept as-is |
| **D3 v3 + NVD3** | Medium | Obsolete — replacement requires refactoring inside each module |
| **`hookDashboardData` AJAX pattern** | High | Implicit contract between `dashboard.js` and modules — must be formalized if JS is migrated |
| **Raw SQL in modules** | Medium | `Db::getInstance()` in all modules — not a blocker for controller migration |

---

## 8. Current Architecture

```
AdminDashboardController (legacy)
  ├── Loads assets: dashboard.js + D3v3 + NVD3 + calendar.js (via setMedia)
  ├── Renders: default/template/controllers/dashboard/helpers/view/view.tpl (Smarty)
  └── Hooks zones → modules (Smarty HTML) + dashboardData (JSON for dashboard.js)

dash* modules (assets via hookActionAdminControllerSetMedia)
  ├── hookDashboardZoneX → Smarty assign + HelperForm + display()
  └── hookDashboardData  → PHP array → dashboard.js (AJAX)
```

---

## 9. Lessons from the Attempted POC

A partial POC was attempted (Symfony controller + Twig template + `AdminDashboardController` shim). Two blockers were confirmed in practice:

**Blocker 1 — `HelperCalendar` unusable in the Symfony context**
`HelperCalendar::generate()` calls `Helper::generate()` which creates a Smarty template via `$context->smarty->createTemplate('helpers/calendar/calendar.tpl')`. In the Symfony context, Smarty does not have the admin theme directories in its search paths. **Resolution**: port `calendar.tpl` directly to Twig — the template is straightforward (~100 lines), the migration is mechanical.

**Blocker 2 — `HelperForm` in module hooks**
`dashactivity::hookDashboardZoneOne()` calls `renderConfigForm()` which instantiates `HelperForm`. It looks for `helpers/form/form.tpl` in Smarty paths, which are not configured. **Resolution**: add the admin theme template directory to Smarty paths before dispatching zone hooks:
```php
$context->smarty->addTemplateDir(_PS_BO_ALL_THEMES_DIR_ . $boTheme . '/template/');
```
It's one line, but it reveals a **structural coupling**: any migrated page that dispatches hooks where modules use legacy `Helper*` classes will need this same setup. This will remain true as long as the modules themselves are not migrated.

**POC conclusion**: migrating only the controller is technically feasible with these two adjustments. The page can be displayed. However, zone rendering remains entirely in Smarty (on the module side) — the migration does not change the user experience, only the controller/routing layer.

---

## 10. Assessment: Should We Start a Real POC?

**Yes, both blockers are solvable without touching the modules.** Honest evaluation:

| Criterion | Status |
|-----------|--------|
| Functional Symfony controller | ✅ Feasible, known patterns |
| Twig template for layout | ✅ Mechanical migration of `view.tpl` |
| Calendar in Twig | ✅ Mechanical migration of `calendar.tpl` |
| Module compatibility (shim + Smarty dirs) | ✅ 2 lines of setup |
| AJAX `dashboard.js` without modifying it | ✅ Preserve same URLs and formats |
| Functional charts | ✅ D3/NVD3 assets loaded manually |
| Estimated effort | ~1 to 2 days of clean work |

The migration provides no visible value to the user but lays the groundwork for subsequently migrating the `dash*` modules to a modern architecture.

---

## 11. First Migration Steps

### Step 1 — Symfony Controller

Create `src/PrestaShopBundle/Controller/Admin/DashboardController.php` extending `PrestaShopAdminController`.

Actions to port:
- `indexAction(Request $request)`: main page + zone hook dispatch
- Inline AJAX (via `?ajax=1&action=X`): `refreshDashboard`, `setSimulationMode`, `saveDashConfig`
- `submitDateRange`: save employee date range

**Mandatory setup** in `indexAction` **before** dispatching hooks:
```php
// 1. Shim for modules' get_class() compatibility
$shim = new AdminDashboardController();
Context::getContext()->controller = $shim;

// 2. Trigger module asset injection
Hook::exec('actionAdminControllerSetMedia');
$moduleJsFiles = array_unique($shim->js_files);

// 3. Open Smarty paths for HelperForm/HelperCalendar in module hooks
$boTheme = $employee->bo_theme ?: 'default';
Context::getContext()->smarty->addTemplateDir(_PS_BO_ALL_THEMES_DIR_ . $boTheme . '/template/');
```

### Step 2 — Routing

Create `src/PrestaShopBundle/Resources/config/routing/admin/dashboard.yml`:
```yaml
admin_dashboard:
  path: /
  methods: [ GET, POST ]
  defaults:
    _controller: 'PrestaShopBundle\Controller\Admin\DashboardController::indexAction'
    _legacy_controller: AdminDashboard
    _legacy_link: AdminDashboard
  options:
    expose: true
```

Add to `admin.yml`:
```yaml
_admin_dashboard_routing:
  resource: "admin/dashboard.yml"
  prefix: /dashboard
```

### Step 3 — Twig Template

Create `src/PrestaShopBundle/Resources/views/Admin/Dashboard/index.html.twig` by porting `view.tpl`:
- Zones via `{{ hookDashboardZoneOne|raw }}` etc. — hooks return HTML
- Calendar: port `calendar.tpl` directly to Twig (no `HelperCalendar`)
- JS variables (`dashboard_ajax_url`, `adminstats_ajax_url`, `translated_dates`) inlined in a `{% block extra_javascripts %}` block
- D3/NVD3/dashboard.js assets loaded via `asset()` in `extra_javascripts`
- Module assets (from `$shim->js_files`) via a Twig loop on `moduleJsFiles`

### Step 4 — `AdminDashboardController` Shim

Empty `AdminDashboardControllerCore` of all rendering and AJAX logic. Keep only the minimal constructor:
```php
public function __construct()
{
    $this->context = Context::getContext();
}
```

> ⚠️ Do not call `parent::__construct()` — too heavy, not needed for a shim.

### Step 5 — Manual Verification

- Main page displays all 4 widgets
- AJAX refresh works (clicking date buttons)
- Demo mode toggles correctly
- `dashboard_ajax_url` points to the new Symfony route
- `getAdminLink('AdminDashboard')` returns the new URL (via `_legacy_link`)

---

## 12. Key Files

| File | Why |
|------|-----|
| `controllers/admin/AdminDashboardController.php` | Legacy controller to empty into a shim |
| `js/admin/dashboard.js` | Frontend JS logic (do not modify) |
| `admin-dev/themes/default/template/controllers/dashboard/helpers/view/view.tpl` | View to port to Twig |
| `admin-dev/themes/default/template/helpers/calendar/calendar.tpl` | Calendar to port to Twig |
| `modules/dashactivity/dashactivity.php` | Reference: hookDashboardZoneOne + HelperForm pattern |
| `src/PrestaShopBundle/Resources/config/routing/admin.yml` | Admin routes aggregator |

---

## 13. Migration Epics and Tickets

Epics are ordered by dependency. Epic 1 is the only prerequisite for the others; Epics 2 and 3 can run in parallel once Epic 1 is delivered.

Indicative sizes: XS < 0.5d — S ≈ 0.5–1d — M ≈ 1–2d — L ≈ 3–5d

---

### Epic 1 — Dashboard Controller Migration _(without touching any module)_

**Goal:** Run the Dashboard on a Symfony controller while keeping all modules intact. Technical value only — no user-visible change.

| # | Ticket | Description | Size |
|---|--------|-------------|------|
| DASH-01 | Symfony controller + routing | Create `DashboardController` extending `PrestaShopAdminController`, routing YAML with `_legacy_link: AdminDashboard` | S |
| DASH-02 | Main Twig template | Port `view.tpl` to `index.html.twig`: hook zones, JS variables, module asset loop | S |
| DASH-03 | Calendar in Twig | Port `calendar.tpl` to Twig, remove `HelperCalendar`, load `date-range-picker.js` + `calendar.js` in `extra_javascripts` | M |
| DASH-04 | `AdminDashboardController` shim | Strip `AdminDashboardControllerCore`, keep only the minimal constructor (`get_class()` compat) | XS |
| DASH-05 | Manual QA | Verify: 4 widgets render, AJAX refresh works, demo mode toggles, `getAdminLink('AdminDashboard')` returns correct URL | S |

---

### Epic 2 — JavaScript Stack Modernisation

**Goal:** Remove the Dashboard JS from the legacy `addJS()` pattern and replace obsolete libraries. Prerequisite: Epic 1.

| # | Ticket | Description | Size |
|---|--------|-------------|------|
| DASH-06 | Bundle `dashboard.js` | Move logic into a new-theme webpack entry point, replace `addJS()` | M |
| DASH-07 | Replace D3v3 + NVD3 | Migrate chart rendering to a modern library (Chart.js, ECharts…) — impacts all `dash*` modules | L |
| DASH-08 | Formalise `hookDashboardData` contract | Define an interface or DTO for the data returned by modules, document the AJAX contract | M |

---

### Epic 3 — Migrate `dash*` Modules

**Goal:** Eliminate `HelperForm`, Smarty, and `Db::getInstance()` from each module. Prerequisite: Epic 1. Can be parallelised per module.

#### 3a — `dashactivity`

| # | Ticket | Description | Size |
|---|--------|-------------|------|
| DASH-09 | Replace `HelperForm` | Migrate `renderConfigForm()` to a Symfony Form Type + Twig | M |
| DASH-10 | Smarty templates → Twig | Port `dashboard_zone_one.tpl` and all hook templates to Twig | M |
| DASH-11 | Doctrine | Replace `Db::getInstance()` with Doctrine repositories | M |

#### 3b — `dashtrends`

| # | Ticket | Description | Size |
|---|--------|-------------|------|
| DASH-12 | Smarty templates → Twig | Port hook templates to Twig | M |
| DASH-13 | Doctrine | Replace `Db::getInstance()` with Doctrine repositories | M |

#### 3c — `dashgoals`

| # | Ticket | Description | Size |
|---|--------|-------------|------|
| DASH-14 | `ConfigurationKPI` entity | Model the `ConfigurationKPI` table as a Doctrine entity + migration | M |
| DASH-15 | Smarty templates → Twig | Port hook templates to Twig | M |
| DASH-16 | Doctrine | Replace `Db::getInstance()` with Doctrine repositories | M |

#### 3d — `dashproducts`

| # | Ticket | Description | Size |
|---|--------|-------------|------|
| DASH-17 | Smarty templates → Twig | Port hook templates to Twig | S |
| DASH-18 | Doctrine | Replace `Db::getInstance()` with Doctrine repositories | M |

---

### Epic 4 — Final Cleanup

**Goal:** Remove all temporary compatibility code. Prerequisite: Epic 3 complete across all modules.

| # | Ticket | Description | Size |
|---|--------|-------------|------|
| DASH-19 | Remove shim | Delete `AdminDashboardControllerCore` or drop it from legacy routing | XS |
| DASH-20 | Remove legacy assets | Drop `d3.v3.min.js`, `nv.d3.min.js` if Epic 2 (DASH-07) is delivered | S |
| DASH-21 | Playwright UI tests | Write E2E tests for the Dashboard page (widget display, date picker, demo mode) | L |

---

### Dependency Summary

```
Epic 1 (DASH-01 → DASH-05)
  ├── Epic 2 (DASH-06 → DASH-08)   ← JS modernisation
  ├── Epic 3a (DASH-09 → DASH-11)  ← dashactivity
  ├── Epic 3b (DASH-12 → DASH-13)  ← dashtrends
  ├── Epic 3c (DASH-14 → DASH-16)  ← dashgoals
  └── Epic 3d (DASH-17 → DASH-18)  ← dashproducts
        └── Epic 4 (DASH-19 → DASH-21)  ← cleanup (waits for full Epic 3)
```
