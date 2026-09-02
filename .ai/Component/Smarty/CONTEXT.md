# Smarty Component

## Purpose

Front-office templating engine: extended Smarty classes with lazy cache invalidation, module and parent-theme template resource handlers, development debug mode, and adapter classes for managing Smarty settings. Still used in the back-office by legacy pages and modules.

## Layers

| Layer | Path |
|-------|------|
| Production + dev engines | `classes/Smarty/SmartyCustom.php`, `classes/Smarty/SmartyDev.php` |
| Module + parent theme resource handlers | `classes/Smarty/SmartyResourceModule.php`, `classes/Smarty/SmartyResourceParent.php` |
| Lazy plugin registry | `classes/Smarty/SmartyLazyRegister.php` |
| Template hierarchy resolver | `classes/Smarty/TemplateFinder.php` |
| Cache settings adapter | `src/Adapter/Smarty/SmartyCacheConfiguration.php` |

## Non-obvious patterns

- `SmartyCustom::clearAllCache()` **marks templates stale** (via `smarty_last_flush` DB timestamp) rather than deleting cache files — avoids race conditions under high traffic
- `SmartyDev` wraps every template output with `<!-- begin /path/to.tpl -->` / `<!-- end -->` HTML comments — useful for debugging template inheritance chains
- `SmartyLazyRegister` registers plugins on first use via `__call()` — prevents loading all plugins on every request; don't bypass it by calling `registerPlugin()` directly

## Canonical examples

- `classes/Smarty/SmartyCustom.php`
- `classes/Smarty/SmartyResourceModule.php`
- `classes/Smarty/TemplateFinder.php`

## Related

- [Twig Component](../Twig/CONTEXT.md) — back-office equivalent; the two engines coexist during migration
- [Configuration Component](../Configuration/CONTEXT.md) — `SmartyCacheConfiguration` uses `DataConfigurationInterface`
