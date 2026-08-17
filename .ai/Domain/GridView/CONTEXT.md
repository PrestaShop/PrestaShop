# GridView Domain

## Purpose

Employee-saved grid views: named, optionally shared snapshots of a back-office grid's filters (plus grid state and dynamic date rules), and the per-employee configuration of the grid views panel. Does NOT handle the grid rendering itself (see the Grid component) nor the persisted filters storage (`ps_admin_filter`, Improve domain).

## Layers

| Layer | Path |
|-------|------|
| Commands / Queries / VOs / Exceptions | `src/Core/Domain/GridView/` |
| Handlers + access & business validation | `src/Adapter/GridView/` |
| Doctrine entities | `src/PrestaShopBundle/Entity/AdminGridView.php`, `AdminGridConfiguration.php` |
| Repositories | `src/PrestaShopBundle/Entity/Repository/AdminGridViewRepository.php`, `AdminGridConfigurationRepository.php` |
| Grid views component (presenters, counter, CSV export, filters builder) | `src/Core/Grid/View/` |
| CRUD form layer (providers/handlers/options provider) | `src/Core/Form/IdentifiableObject/` |
| Controller | `src/PrestaShopBundle/Controller/Admin/GridViewController.php` |

## Non-obvious patterns

- **Saved criteria are never read from the client**: `AddGridViewHandler` reads the employee's persisted filters from `ps_admin_filter` (`AdminFilterRepository`); only `grid_state` and `dynamic_date_rules` come from the form and are sanitized server-side (`GridViewDataSanitizer`).
- **Access rules live in `Adapter/GridView/GridViewProvider`**: `getOwnedGridView()` (edit/delete — owner only) vs `getAccessibleGridView()` (duplicate/export — owner or shared), both also requiring shop authorization. Handlers AND the controller reuse it; violations throw `GridViewNotFoundException` / `GridViewAccessDeniedException`, mapped to 404/403 in the controller.
- **`SaveGridConfigurationCommand` is an upsert** keyed on (employee, shop, grid id, route) — `AdminGridConfigurationRepository::findOrCreateForEmployee()` recovers from concurrent-insert unique violations via DBAL insert.
- **The employee/shop context is resolved in the handlers** (`EmployeeContext`/`ShopContext`), not carried by the commands: the actor is never client-supplied.
- **`filter_id` must belong to the grid** (equal to the grid id or prefixed by `{gridId}_` for dynamic grids) — validated in the command constructors (`AbstractGridViewCommand`).
- The grid view forms follow the identifiable-object CRUD pattern with **static form names** (`grid_view`, `grid_configuration`); the target grid travels in a hidden `grid_id` field that the controller cross-checks against the route parameter.
- `Edit` never touches the stored filters or grid state — only name, shared flag and dynamic date rules; the dynamic date rule fields of the edit form are computed by `GetGridViewForEditingHandler` (`EditableGridView::getDateRangeFilterFields()`) and injected via `GridViewFormOptionsProvider`.
- The view limit (`GridViewSettings::MAX_VIEWS_PER_CONFIGURATION`) throws `GridViewLimitReachedException`, the only domain error with a dedicated user-facing message.
- Edit/delete handlers invalidate the per-view record count cache (`GridViewCounter::CACHE_KEY_PREFIX`).

## Canonical examples

- `src/Adapter/GridView/CommandHandler/AddGridViewHandler.php` — server-side criteria read + sanitization + limit check
- `src/Adapter/GridView/GridViewProvider.php` — ownership/shared access rules
- `src/Core/Form/IdentifiableObject/DataHandler/GridViewFormDataHandler.php` — command bus usage from the form layer

## Related

- [Component/Grid/CONTEXT.md](../../Component/Grid/CONTEXT.md) — grid views panel, counter, CSV export, `GridFactoryProvider`
- [Component/Forms/CONTEXT.md](../../Component/Forms/CONTEXT.md) — identifiable-object CRUD form pattern
- [Component/CQRS/CONTEXT.md](../../Component/CQRS/CONTEXT.md) — command/query conventions
