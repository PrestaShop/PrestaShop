# Profile Domain

## Purpose

Manages employee profiles (roles/groups), including CRUD operations and the assignment of tab/module permissions per profile. It does NOT handle authentication or individual employee session management (see Security domain).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Profile/` — Commands, Queries, QueryResults, ValueObjects, Exceptions, Permission sub-domain |
| Adapter | `src/Adapter/Profile/` — handler implementations, `ProfileDataProvider`, Permission handlers, Employee sub-handlers |
| Legacy ObjectModel | `classes/Profile.php` (250 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/ProfileController.php` |

## Non-obvious patterns

- The domain has a nested `Permission/` sub-domain (both in Core and Adapter) that handles tab and module permission updates independently from profile CRUD.
- The Adapter has an `Employee/` subdirectory with an `AbstractEmployeeHandler` — profile changes can cascade to employee-level side effects handled there.
- `ProfileSettings.php` in the Core domain holds configuration constants, not a value object.

## Canonical examples

- `src/Core/Domain/Profile/Command/AddProfileCommand.php` + `src/Adapter/Profile/CommandHandler/AddProfileHandler.php`
- `src/Core/Domain/Profile/Query/GetProfileForEditing.php` + `src/Adapter/Profile/QueryHandler/GetProfileForEditingHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Profile/` — Behat behavior scenarios
