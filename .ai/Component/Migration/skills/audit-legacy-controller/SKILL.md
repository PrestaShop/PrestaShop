---
name: audit-legacy-controller
description: >
  Read the legacy `Admin{Domain}sController.php` without modifying it. Extract
  every field rendered, every action method, every `Hook::exec()` call, and
  the behaviour around the save (normalisation, extra validations, stored formats).
produces: "Field map, action list (CRUD/bulk/toggle), hook inventory and behaviour inventory from the legacy controller"
subagent: recommended
---

# audit-legacy-controller

## Instructions

1. Open `controllers/admin/Admin{Domain}sController.php` — read the full file.
2. List every public action method: `renderList`, `renderForm`, `postProcess`, `ajaxProcess*`, any custom method.
3. For each form field in `renderForm()`, record: field name, type (text/select/checkbox/file), whether it is translatable, whether it is required.
4. Record every DB column accessed in `getList()` or `renderList()` — these map to grid columns.
5. Record every filter defined in `renderList()` — these map to grid filter types.
6. List every `Hook::exec()` call with the hook name and arguments passed.
7. Note any multistore-specific branches (`if (Shop::isFeatureActive())`).
8. Record whether the controller handles file uploads (logo, image), and every image format and image type its upload loop generates.
9. Inventory the behaviour, not only the structure, following [Migration/CONTEXT.md → Behaviour parity](../../CONTEXT.md#behaviour-parity). Read every method listed there that the controller overrides (and the `AdminController` defaults it relies on), and for each one record what it does to the data: a value it normalises or reformats before saving, a validation it adds, a key or column it writes that the form does not show.
10. For each options block (`fields_options`), record every `updateOption{Key}()` callback and the exact value it stores (which property, read in which language).
11. Record the create-form defaults, the order in which `renderForm()` renders the fields, and what the controller stores when an optional field is submitted empty.
12. Record the legacy controller's `$this->table`: the migrated routes' `_legacy_link` values are built from it.
13. Output: a structured field map grouped by (form fields, list columns, filters, hooks, actions, behaviour). Each behaviour line names the method and line it comes from, so the manifest can carry it into the parity checklist.

## Rules

- Never modify the controller file — this is a read-only audit
- Note every `ObjectModel` method called (`save()`, `update()`, `delete()`) to inform handler implementation
- Flag any field that uses `Tools::getValue()` directly without validation — these need ValueObject wrapping
- Record position/sort fields explicitly — they trigger `create-position-column` (G)
