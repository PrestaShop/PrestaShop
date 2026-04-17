# Configuration Component

## Purpose

Typed, multi-store-aware abstraction over PrestaShop's key-value configuration store (`configuration`). Handles reading and writing shop settings across all-shops, shop-group, and single-shop scopes. Does not manage Symfony container parameters.

## Layers

| Layer | Path |
|-------|------|
| Core contracts | `src/Core/Configuration/` — `DataConfigurationInterface`, `AbstractMultistoreConfiguration`, `IniConfiguration`, `UploadSizeConfiguration` |
| Adapter | `src/Adapter/Configuration.php` — main adapter wrapping legacy `Configuration` ObjectModel |
| Domain configs | `src/Adapter/Configuration/*Configuration.php` + 20+ in `src/PrestaShopBundle/` — one per "Configure" settings page |

## Non-obvious patterns

- `AbstractMultistoreConfiguration` automatically handles per-shop override checkboxes and `ShopConstraint` scoping — extend it for any new multi-store-aware settings page
- `Configuration` adapter extends `ParameterBag` and falls back to PHP constants if a key is defined as one
- `DataConfigurationInterface` is the settings-page equivalent of `FormDataHandlerInterface` — every "Configure" section implements it

## Canonical examples

- `src/Core/Configuration/AbstractMultistoreConfiguration.php`
- `src/Adapter/Configuration.php`

## Related

- [Context Component](../Context/CONTEXT.md) — `ShopConstraint` from `ShopContext` scopes config writes
- [Forms Component](../Forms/CONTEXT.md) — settings pages use `DataConfigurationInterface` instead of `FormDataHandlerInterface`
