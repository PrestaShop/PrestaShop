# GlobalJS Component

## Purpose

Exposes Symfony routing metadata to the back-office JavaScript layer so frontend code can generate backend URLs dynamically, and provides a centralized DOM selector map. Does not contain business logic.

## Layers

| Layer | Path |
|-------|------|
| Twig extension (base_url + CSRF) | `src/PrestaShopBundle/Twig/Extension/JsRouterMetadataExtension.php` |
| Generated route metadata (500+ routes) | `admin-dev/themes/new-theme/js/fos_js_routes.json` |
| Shared DOM selector map | `admin-dev/themes/new-theme/js/global-map.ts` |

## Non-obvious patterns

- `fos_js_routes.json` is **generated**, not hand-maintained — run `bin/console fos:js-routing:dump --format=json` after adding or renaming Symfony routes
- `JsRouterMetadataExtension` is injected into the base admin layout automatically — no per-page setup needed

## Canonical examples

- `src/PrestaShopBundle/Twig/Extension/JsRouterMetadataExtension.php`
- `admin-dev/themes/new-theme/js/fos_js_routes.json`

## Related

- [Router Component](../Router/CONTEXT.md) — Symfony routes consumed to generate `fos_js_routes.json`
- [Twig Component](../Twig/CONTEXT.md) — `JsRouterMetadataExtension` registered as a Twig extension
