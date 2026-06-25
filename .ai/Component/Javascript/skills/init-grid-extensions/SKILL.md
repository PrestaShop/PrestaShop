---
name: init-grid-extensions
description: >
  Enable JavaScript grid extensions for a listing page. Extensions add sorting,
  filtering, bulk actions, column toggling, position reordering, and other
  interactive behaviors to the grid. Trigger: "add grid extensions for {Domain}",
  "enable grid extensions for {Domain}", "initialize grid JS for {Domain}".
needs: [create-ts-entry-point, create-grid-definition]
produces: "Grid extensions initialized in the listing entry point"
---

# init-grid-extensions

Read `@.ai/Component/Javascript/CONTEXT.md` for the grid component and extension system.

## Basic pattern

In the listing entry point (`index.ts`):

```typescript
const grid = new window.prestashop.component.Grid('{domain}');

grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
grid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
```

The grid ID must match the `GRID_ID` const from the PHP grid definition factory.

## Available extensions

### Core extensions (most grids need these)

| Extension | Purpose |
|---|---|
| `SortingExtension` | Clickable column headers for sorting |
| `FiltersResetExtension` | Reset button for grid filters |
| `FiltersSubmitButtonEnablerExtension` | Enable/disable filter submit button |
| `BulkActionCheckboxExtension` | Select-all and per-row checkboxes |
| `SubmitBulkActionExtension` | Submit bulk action form (enable/disable/delete) |
| `SubmitRowActionExtension` | Handle row action link clicks |
| `LinkRowActionExtension` | Navigate to row action URLs |
| `ColumnTogglingExtension` | Show/hide columns via dropdown |

### Specialized extensions (add only when needed)

| Extension | Purpose | When to use |
|---|---|---|
| `AsyncToggleColumnExtension` | AJAX toggle for boolean columns | Entity has toggle status in grid |
| `PositionExtension` | Drag-and-drop row reordering | Entity has position column |
| `AjaxBulkActionExtension` | AJAX bulk actions (no page reload) | Performance-sensitive bulk ops |
| `ExportToSqlManagerExtension` | Export grid data to SQL manager | Grid needs SQL export |
| `ReloadListExtension` | Reload grid data without page refresh | AJAX-driven grid updates |
| `PreviewExtension` | Inline row preview expansion | Entity has preview/details panel |
| `ModalFormSubmitExtension` | Submit forms in modal dialogs | Delete confirmation modals |
| `BulkOpenTabsExtension` | Open multiple items in new tabs | Batch review workflows |
| `SubmitGridActionExtension` | Grid-level action buttons | Grid has toolbar actions (export, import) |

### Domain-specific extensions

Some domains have custom extensions in subdirectories:
- `column/catalog/` — catalog-specific column extensions
- `action/row/` — domain-specific row action extensions (e.g. customer delete with order check)

Check `admin-dev/themes/new-theme/js/components/grid/extension/` for the full list.

## Multiple grids on one page

For pages with multiple grids (e.g. Manufacturer with manufacturers + addresses):

```typescript
const manufacturerGrid = new window.prestashop.component.Grid('manufacturer');
manufacturerGrid.addExtension(new SortingExtension());
// ...

const addressGrid = new window.prestashop.component.Grid('manufacturer_address');
addressGrid.addExtension(new SortingExtension());
// ...
```

Each grid instance gets its own set of extensions.

## Rules

Conventions (multiple Grid instances pattern) are in [Javascript/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Grid ID must match the PHP `GRID_ID` constant — mismatch means extensions don't bind
- Only add extensions that match the grid definition — don't add PositionExtension without a PositionColumn
- AsyncToggleColumnExtension requires a matching AJAX toggle route in the controller
