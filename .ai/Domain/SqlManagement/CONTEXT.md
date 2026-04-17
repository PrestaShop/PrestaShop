# SqlManagement Domain

## Purpose

Manages saved SQL queries (called "SQL requests") that back-office users can store and execute against the database, and exposes database table/field introspection. It does not execute arbitrary schema changes — only SELECT-style queries entered by admins.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/SqlManagement/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/SqlManager/` — handler implementations (note: folder is `SqlManager`, not `SqlManagement`) |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/SqlManagerController.php` |

## Non-obvious patterns

- The adapter folder is `src/Adapter/SqlManager/` (without "ment"), while the Core domain is `SqlManagement` — these names do not match.
- Several handler interfaces are defined in `src/Core/Domain/SqlManagement/CommandHandler/` and `QueryHandler/` but most concrete implementations live only in the adapter; `SaveSqlRequestSettingsHandler` and `GetSqlRequestSettingsHandler` are exceptions that are implemented directly in Core.
- `DatabaseTablesList.php`, `DatabaseTableFields.php`, `SqlRequestExecutionResult.php`, and `SqlRequestSettings.php` are standalone result/settings value objects at the domain root (not in a `QueryResult/` subfolder).

## Canonical examples

- `src/Core/Domain/SqlManagement/Command/AddSqlRequestCommand.php` + `src/Adapter/SqlManager/CommandHandler/AddSqlRequestHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
