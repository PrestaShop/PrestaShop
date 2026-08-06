# Router Component

## Purpose

Back-office URL generation with security token injection and legacy route mapping: wraps Symfony's router to append tokens to all admin URLs, maps legacy `Controller:Action` pairs to Symfony routes, and provides entity-specific link builders. Does not handle front-office URL rewriting — that is the Link component.

## Layers

| Layer | Path |
|-------|------|
| Token-injecting router | `src/PrestaShopBundle/Service/Routing/Router.php` |
| Legacy route mapping | `src/PrestaShopBundle/Routing/Converter/` |
| Entity link builder contracts + factory | `src/Core/Routing/` |
| Adapter link builders | `src/Adapter/Routing/` |
| Legacy-compatible URL generator | `src/Adapter/Admin/UrlGenerator.php` |

## Non-obvious patterns

- Token injection is **transparent** — all `generateUrl()` / `redirectToRoute()` calls in back-office controllers get the token automatically via the custom `Router`; no manual token append needed
- Routes marked with `_anonymous_controller` attribute skip token injection — used for login and public API endpoints
- `_legacy_link` route attribute enables the reverse mapping: `RouterProvider` builds `LegacyRoute` objects from this attribute to support old `Link::getAdminLink()` callers during migration
- `_legacy_feature_flag` maps the routing to a specific feature flag. Depending on the feature flag status, the automatic legacy link conversion is enabled/disabled

## Canonical examples

- `src/PrestaShopBundle/Service/Routing/Router.php`
- `src/PrestaShopBundle/Routing/Converter/RouterProvider.php`
- `src/Adapter/Routing/AdminLinkBuilder.php`

## Related

- [Link Component](../Link/CONTEXT.md) — front-office URL generation and legacy `Link` class
