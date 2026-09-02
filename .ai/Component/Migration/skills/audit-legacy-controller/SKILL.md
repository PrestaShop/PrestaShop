---
name: audit-legacy-controller
description: >
  Read the legacy `Admin{Domain}sController.php` without modifying it. Extract
  every field rendered, every action method, and every `Hook::exec()` call.
produces: "Field map, action list (CRUD/bulk/toggle), hook inventory from the legacy controller"
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
8. Record whether the controller handles file uploads (logo, image).
9. Output: a structured field map grouped by (form fields, list columns, filters, hooks, actions).

## Rules

- Never modify the controller file — this is a read-only audit
- Note every `ObjectModel` method called (`save()`, `update()`, `delete()`) to inform handler implementation
- Flag any field that uses `Tools::getValue()` directly without validation — these need ValueObject wrapping
- Record position/sort fields explicitly — they trigger `create-position-column` (G)
