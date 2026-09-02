---
name: create-grid-definition
description: >
  Create the grid definition for an entity listing: columns, row actions, bulk actions,
  filters class, and service registration. Covers everything needed to define the grid
  structure. The query builder is a separate skill (create-grid-query-builder).
  Trigger: "create grid definition for {Domain}".
needs: [create-cqrs-commands, create-admin-routing]
produces: "{Domain}GridDefinitionFactory + {Domain}Filters + DI registration"
subagent: optional
---

# create-grid-definition

Read `@.ai/Component/Grid/CONTEXT.md` for the factory trilogy (GridDefinitionFactory → GridDataFactory → GridFactory) and SearchCriteria patterns.

## 1. Grid Definition Factory

Create `src/Core/Grid/Definition/Factory/{Domain}GridDefinitionFactory.php` extending `AbstractGridDefinitionFactory`:

- Define a `public const GRID_ID = '{domain}'` constant — this is the single source of truth for the grid identifier, shared with the `{Domain}Filters` class to ensure the filter persistence in DB maps to the correct grid
- `getId(): string` — return `self::GRID_ID`
- `getName(): string` — translatable grid name
- `getColumns(): ColumnCollection` — all columns (see section 2)
- `getFilters(): FilterCollection` — filterable columns (see section 4)
- `getGridActions(): GridActionCollection` — grid-level actions (e.g. export)
- `getRowActions(): RowActionCollection` — per-row actions (see section 3)
- `getBulkActions(): BulkActionCollection` — multi-select actions (see section 3)

**Reference:** `src/Core/Grid/Definition/Factory/TaxGridDefinitionFactory.php` (simple), `src/Core/Grid/Definition/Factory/ManufacturerGridDefinitionFactory.php` (two grids)

## 2. Column types

| Column type | When to use | Notes |
|---|---|---|
| `BulkActionColumn` | Row selection checkbox | Always first column |
| `DataColumn` | Plain text (name, email, date) | Most common |
| `ToggleColumn` | Clickable boolean toggle (active status) | Requires AJAX toggle route |
| `ImageColumn` | Image thumbnail (logo) | |
| `LinkColumn` | Text with hyperlink | |
| `PositionColumn` | Drag handle for reordering | See `create-position-column` skill |
| `ActionColumn` | Row actions dropdown | Always last column |

See [Grid/CONTEXT.md](../../CONTEXT.md#column-definitions) for column ordering and naming conventions.

## 3. Row actions and bulk actions

### Row actions

Add in `getRowActions()`:
- `LinkRowAction` for edit — links to `admin_{domain}s_edit` route with `{id}` parameter
- For delete, **use the `DeleteActionTrait::buildDeleteAction()` helper** — it returns a `SubmitRowAction` pre-wired with the standard delete confirmation modal (translatable title, confirm/cancel buttons, danger styling). Prefer it over building the action manually:
  ```php
  use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\DeleteActionTrait;

  class {Domain}GridDefinitionFactory extends AbstractGridDefinitionFactory
  {
      use DeleteActionTrait;

      protected function getRowActions(): RowActionCollection
      {
          return (new RowActionCollection())
              ->add(/* edit LinkRowAction */)
              ->add($this->buildDeleteAction(
                  'admin_{domain}s_delete',  // route
                  '{domain}Id',              // route param name
                  '{domain}Id',              // row field providing the value
              ));
      }
  }
  ```
- Order: edit first, delete last

### Bulk actions

Add in `getBulkActions()`:
- `SubmitBulkAction` for each bulk operation (delete, enable, disable, or any other)
- Each submits the grid form to the corresponding controller route
- Bulk delete must include a confirmation dialog (`confirm_bulk_action: true`)
- Only add bulk actions that the entity actually supports — not all entities have enable/disable

Route names in actions must exactly match the routing YAML.

## 4. Filters

Filters are defined in the grid definition via `getFilters(): FilterCollection` (see [Grid/CONTEXT.md](../../CONTEXT.md#filters) for conventions). Each filter specifies:
- Filter ID (must match a column ID)
- The form type to render (e.g. `TextType`, `ChoiceType`, `DateRangeType`)

## 5. {Domain}Filters class (SearchCriteria)

Create `src/Core/Search/Filters/{Domain}Filters.php` extending `Filters`:

- This implements `SearchCriteriaInterface` — it is NOT a form type
- Uses the `GRID_ID` constant from the definition factory to ensure the filter ID matches the grid — this is critical for filter persistence in DB
- Defines defaults: grid ID, default sort column, sort direction, limit
- Injected into the controller's `indexAction` via argument resolver
- Filter values are populated from the session (saved by `CommonController::searchGridAction`)

```php
class {Domain}Filters extends Filters
{
    protected $filterId = {Domain}GridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => static::LIST_LIMIT,
            'offset' => 0,
            'orderBy' => 'id_{domain}',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
```

**Reference:** `src/Core/Search/Filters/TaxFilters.php` (simple)

## 6. Service registration

Register in DI YAML:

- `{Domain}GridDefinitionFactory` — with parent `prestashop.core.grid.definition.factory.abstract_grid_definition` and grid ID
- `{Domain}Filters` — with `autoconfigure: true`
- Wire the GridFactory service combining definition factory + data factory
- Verify: `php bin/console debug:container | grep {domain}`

## Rules

Conventions (column ordering, toggle route, Filters as SearchCriteria) are in [Grid/CONTEXT.md](../../CONTEXT.md). Skill-specific reminder:

- Route names in actions must match routing YAML exactly — typos cause silent 404s
