# Console Component

## Purpose

CLI infrastructure for PrestaShop: a custom Symfony Application wrapper supporting multi-kernel execution, a base class for running commands programmatically, and 24 concrete administrative commands. Does not handle HTTP requests.

## Layers

| Layer | Path |
|-------|------|
| Application wrapper | `src/PrestaShopBundle/Console/PrestaShopApplication.php` |
| Programmatic runner | `src/PrestaShopBundle/Service/Command/AbstractCommand.php` |
| Commands | `src/PrestaShopBundle/Command/` — 24 commands registered via `#[AsCommand]` |

## Non-obvious patterns

- `PrestaShopApplication` adds `--app-id` to switch between kernels (`admin`, `admin_api`, `front`) at CLI level
- `AbstractCommand` allows running Symfony commands **from PHP code** (not CLI) with buffered output — used by module installers and upgrade scripts

## Canonical examples

- `src/PrestaShopBundle/Console/PrestaShopApplication.php`
- `src/PrestaShopBundle/Command/ModuleCommand.php`

## Related

- [CQRS Component](../CQRS/CONTEXT.md) — some commands dispatch CQRS commands via `CommandBus`
