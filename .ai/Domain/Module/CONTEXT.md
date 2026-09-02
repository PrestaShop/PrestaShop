# Module Domain

## Purpose

Handles lifecycle operations for PrestaShop modules: install, uninstall, upgrade, reset, enable/disable, and upload. Does not manage module configuration (each module owns its own config) or tab registration (handled by event subscribers in the Adapter layer).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Module/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Module/` — handler implementations, `AdminModuleDataProvider`, `ModuleDataProvider`, `ModuleDataUpdater`, `Configuration/`, `Tab/`, `Repository/` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/ModuleController.php` |

## Non-obvious patterns

- The primary identity for most commands is `ModuleTechnicalName` (a string value object), not an integer `ModuleId`. `ModuleId` exists but is secondary.
- Tab registration/unregistration on module install/uninstall is handled by event subscribers in `src/Adapter/Module/Tab/` (`ModuleTabManagementSubscriber`), not in command handlers.
- `src/Adapter/Module/Module.php` is a domain wrapper around the legacy `Module` ObjectModel; it is not an ObjectModel itself.
- The Module domain has no legacy `classes/Module.php` — the legacy class lives outside the structured `classes/` layout and is referenced via the `Module as LegacyModule` alias in adapters.

## Canonical examples

- `src/Core/Domain/Module/Command/InstallModuleCommand.php` + `src/Adapter/Module/CommandHandler/InstallModuleHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Module/` — Behat behavior scenarios
