# Configuration Domain

## Purpose

Provides shop-aware access to the PS configuration key-value store and manages debug-mode toggling. Does not implement per-feature configuration forms — those live in their respective feature domains using `DataConfigurationInterface` adapters.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Configuration/` — `SwitchDebugModeCommand`; `ShopConfigurationInterface` |
| Adapter | `src/Adapter/Configuration/` — command handler, feature-specific `DataConfiguration` adapters |
| Legacy ObjectModel | `classes/Configuration.php` (748 lines) — do not add logic here |

## Non-obvious patterns

- `ShopConfigurationInterface` (in the domain root, not under `Command/`) extends `ConfigurationInterface` with a `ShopConstraint` parameter, enabling multistore-scoped reads. This is the injection point for all feature code that reads configuration.
- `src/Adapter/Configuration/` contains standalone `DataConfigurationInterface` implementations (e.g., `DatabaseLogsConfiguration`, `LogsConfiguration`, `KpiConfiguration`) that are used by feature forms outside the CQRS flow — they are not handlers.
- The domain has only a single Command (`SwitchDebugModeCommand`); most configuration mutations go through `ShopConfigurationInterface::set()` directly, bypassing CQRS.

## Canonical examples

- `src/Core/Domain/Configuration/Command/SwitchDebugModeCommand.php` + `src/Adapter/Configuration/CommandHandler/SwitchDebugModeHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
