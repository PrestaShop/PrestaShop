---
name: create-controller-listing
description: >
  Create the listing page actions in the admin controller: index (grid with filters),
  delete, toggle status, and any bulk actions the entity requires. Trigger:
  "create listing for {Domain}", "create index action for {Domain}".
needs: [create-cqrs-commands, create-grid]
produces: "indexAction, deleteAction, toggleStatusAction, bulk action methods in {Domain}Controller.php"
---

# create-controller-listing

Read `@.ai/Component/Controller/CONTEXT.md` for controller conventions (base class, DI, security attributes, error mapping).
Read `@.ai/Component/Grid/CONTEXT.md` for grid patterns (definition factory, query builder, filters).

## 1. Index action

```php
#[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
public function indexAction({Domain}Filters $filters): Response
```

- Use the `{Domain}Filters` class as action argument — automatically resolved by the argument resolver
- Build grid via the grid factory service, present it, render the index template
- No manual `SearchCriteria` construction needed

**Reference:** `TaxController::indexAction()`, `ManufacturerController::indexAction()`

## 2. Search and reset

- **Search/filter:** `CommonController::searchGridAction` saves and applies grid filters, then redirects back to the domain's index action. A domain-specific route must be defined pointing to this generic controller action (see `create-admin-routing`)
- **Filter reset:** uses the generic route `admin_common_reset_search_by_filter_id` — no domain-specific route or controller code needed. Referenced from the `GridDefinition`

## 3. Delete action

```php
#[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
public function deleteAction(int ${domain}Id): RedirectResponse
```

- Dispatch `Delete{Domain}Command` via command bus
- Catch `{Domain}NotFoundException` and `CannotDelete{Domain}Exception`
- Flash appropriate message, redirect to index

## 4. Toggle status action

```php
#[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
public function toggleStatusAction(int ${domain}Id): JsonResponse
```

- Dispatch `Toggle{Domain}StatusCommand` via command bus
- Return `JsonResponse` (AJAX grid toggle switch)

## 5. Bulk actions

Create one method per bulk action the grid defines — not all entities have the same set:

```php
#[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
public function bulkDeleteAction(Request $request): RedirectResponse
```

Common pattern for all bulk actions:
- Extract selected IDs from request
- If empty selection, return early with info flash
- Dispatch the appropriate bulk command
- Catch bulk exceptions, flash with failed IDs (see [Controller/CONTEXT.md](../../CONTEXT.md))
- Redirect to index

## Rules

See [Controller/CONTEXT.md](../../CONTEXT.md) for all conventions (Filters argument, bulk exception handling, toggle JSON). Skill-specific reminder:

- Bulk actions match the grid definition — don't assume delete/enable/disable are always present
