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
- `_parent` route attribute fills a gap that, before it existed, was only fixable by misusing `_legacy_controller`: fully-native routes (no legacy heritage) reached only from a listing page — `create`/`edit`/`view` actions — have no `Tab` of their own, so the sidebar highlight, breadcrumb, and toolbar had nothing to resolve against. `_parent` names another route (typically the listing/index route) whose `Tab` should be borrowed instead. `MenuBuilder::getParentTab()` resolves it to that route's `Tab` via `TabRepository::findOneByRouteName()`; `NavBar`, `Toolbar`, and `MenuBuilder::getBreadcrumbLinks()` all call `getCurrentTab() ?? getParentTab()` so tab-less pages still render as if they belonged to their parent. Unlike `_legacy_controller`, `_parent` has no effect on permissions or `Link::getAdminLink()` — it is purely BO navigation rendering
- **Do not add `_legacy_controller` to a route just to make navigation (active tab / breadcrumb) work.** That was the only workaround available before `_parent` existed, but `_legacy_controller` is meant for pages actually migrated from a legacy `AdminXController` — see [Controller/CONTEXT.md](../Controller/CONTEXT.md). On a route with no legacy heritage, use `_parent` instead

## Canonical examples

- `src/PrestaShopBundle/Service/Routing/Router.php`
- `src/PrestaShopBundle/Routing/Converter/RouterProvider.php`
- `src/Adapter/Routing/AdminLinkBuilder.php`
- `src/PrestaShopBundle/Twig/Layout/MenuBuilder.php` — `getParentTab()` resolves `_parent` to a `Tab`

## Related

- [Link Component](../Link/CONTEXT.md) — front-office URL generation and legacy `Link` class
