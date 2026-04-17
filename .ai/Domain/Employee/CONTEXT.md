# Employee Domain

## Purpose

Manages back-office employee accounts: creation, editing, deletion, password management, and profile/avatar handling. Does NOT manage customer accounts or front-office authentication — those belong to the Customer domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Employee/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter (infrastructure helpers) | `src/Adapter/Employee/` — `EmployeeRepository.php`, `EmployeeDataProvider.php`, `AvatarProvider.php`, `ContextEmployeeProvider.php`, `EmployeeFormAccessChecker.php`, `EmployeeLanguageUpdater.php`, `FormLanguageChanger.php`, `NavigationMenuToggler.php` |
| Adapter (CQRS handlers) | `src/Adapter/Profile/Employee/CommandHandler/` and `src/Adapter/Profile/Employee/QueryHandler/` — concrete handler implementations |
| Legacy ObjectModel | `classes/Employee.php` (748 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/EmployeeController.php`, `src/Core/Grid/Definition/Factory/EmployeeGridDefinitionFactory.php` |

## Non-obvious patterns

- `ContextEmployeeProvider` is an old service that should not be used in new code (will be refactored and deprecated). Prefer `EmployeeContext` from the Context component instead.
- `EmployeeFormAccessChecker` enforces that employees can only edit their own profile or profiles with lower permission levels; this access check is done in the adapter layer, not in the controller.
- `NavigationMenuToggler` and `FormLanguageChanger` are adapter-level services that handle UI-state side-effects triggered by employee commands (collapsing the nav menu, switching the form language) — they live here because they are per-employee preferences.
- A separate `Security/Session/EmployeeGridDefinitionFactory` exists for the employee session listing (active sessions), distinct from the main employee list grid.

## Canonical examples

- `src/Core/Domain/Employee/Command/AddEmployeeCommand.php` + `src/Adapter/Profile/Employee/CommandHandler/AddEmployeeHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Employee/` — Behat behavior scenarios
