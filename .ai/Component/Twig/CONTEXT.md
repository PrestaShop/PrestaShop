# Twig Component

## Purpose

Back-office templating infrastructure: Twig extensions that expose PrestaShop-specific functions and filters (hooks, localization, admin links, grid columns, translations) to templates. Does not define page templates — those live in `src/PrestaShopBundle/Resources/views/Admin/`. Front-office uses Smarty, not Twig.

## Layers

| Layer | Path |
|-------|------|
| Twig extensions (12+) | `src/PrestaShopBundle/Twig/Extension/`, `src/PrestaShopBundle/Twig/` |

## Non-obvious patterns

- `LayoutExtension` implements `GlobalsInterface` — its variables (`theme`, `default_currency`, `root_url`, `js_translatable`, `rtl_suffix`) are available in **every** back-office template without injection
- `HookExtension` is the only bridge from Twig to `HookDispatcherInterface` — every `{% renderhook 'hookName' %}` call goes through it; never call the dispatcher directly from a template
- `GridExtension` renders column content by column type — it is called from `@PrestaShop/Admin/Common/Grid/Columns/` partials, not from feature templates directly

## Canonical examples

- `src/PrestaShopBundle/Twig/HookExtension.php`
- `src/PrestaShopBundle/Twig/Extension/GridExtension.php`
- `src/PrestaShopBundle/Twig/LayoutExtension.php`

## Related

- [Hook Component](../Hook/CONTEXT.md) — `HookExtension` delegates to `HookDispatcherInterface`
- [Grid Component](../Grid/CONTEXT.md) — `GridExtension` renders column content by type
- [Locale Component](../Locale/CONTEXT.md) — `LocalizationExtension` uses `LocaleRepository`
- [GlobalJS Component](../GlobalJS/CONTEXT.md) — `JsRouterMetadataExtension` feeds the JS router
- [Smarty Component](../Smarty/CONTEXT.md) — front-office counterpart
