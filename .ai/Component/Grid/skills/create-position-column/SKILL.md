---
name: create-position-column
description: >
  Documents how to add drag-and-drop row reordering to a PrestaShop grid.
  Requires a PositionColumn in the definition, a ReorderPositionsButtonType
  filter, a dedicated update-position route, and position handling in the
  repository.
needs: [create-grid-definition, create-admin-routing]
produces: "PositionColumn + ReorderPositionsButtonType filter and position-update route wiring"
conditional: "only for entities with position/sort support"
---

# create-position-column

Canonical reference: [`FeatureValueGridDefinitionFactory`](../../../../../src/Core/Grid/Definition/Factory/FeatureValueGridDefinitionFactory.php).

## Instructions

1. Add `PositionColumn` as the second column (after BulkActionColumn) in the Grid Definition.
2. Configure the position update route: `->setOption('update_method', 'POST')->setOption('update_route', 'admin_{domain}s_update_position')`.
3. **Add a `ReorderPositionsButtonType` filter associated with the position column** (see Rules below) — mandatory, not optional.
4. Create the `admin_{domain}s_update_position` POST route in the routing YAML (see `create-admin-routing` skill).
5. In the controller, handle the AJAX position update: receive `positions[]` array, dispatch `UpdatePosition{Domain}Command` (or use QueryBuilder directly).
6. In the repository, update the `position` column for the moved entities.

## Rules

Column ordering conventions (PositionColumn as second column) are in [Grid/CONTEXT.md](../../CONTEXT.md#column-definitions). Skill-specific reminders:

- **Always pair the PositionColumn with a `ReorderPositionsButtonType` filter.** It renders a "Rearrange" button (`js-btn-reorder-positions`) that toggles inline drag-and-drop mode — dramatically better UX than a numeric position search field. Use:

  ```php
  use PrestaShopBundle\Form\Admin\Type\ReorderPositionsButtonType;

  ->add((new Filter('position', ReorderPositionsButtonType::class))
      ->setAssociatedColumn('position')
  );
  ```

- Position updates are always AJAX — return JSON response, not redirect
- Position values start at 0 or 1 — be consistent with existing PS convention
