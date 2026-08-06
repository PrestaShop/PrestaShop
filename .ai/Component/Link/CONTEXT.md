# Link Component

## Purpose

URL generation for front-office (SEO-friendly product/category/CMS URLs) and back-office (legacy controller links with security tokens). Bridges the legacy `Link` class with modern Symfony routing via adapters. Does not handle routing configuration — only URL string generation.

## Layers

| Layer | Path |
|-------|------|
| Legacy URL generator (1685 lines) | `classes/Link.php` |
| Entity link builder contracts + factory | `src/Core/Routing/` |
| Adapter link builders | `src/Adapter/Routing/` |
| Legacy-compatible URL generator | `src/Adapter/Admin/UrlGenerator.php` |

## Non-obvious patterns

- `Link::getAdminLink()` injects the security token — never call it directly in new back-office code; use `AdminLinkBuilder` or the Symfony router instead
- `UrlGenerator` in `src/Adapter/Admin/` converts Symfony route names to legacy admin URLs via `_legacy_controller` route metadata — this is the migration bridge, not a standard Symfony URL generator
- `Link::$cache['page']` is a static request-level cache for URL deduplication — be aware when writing tests that rely on URL generation

## Canonical examples

- `classes/Link.php`
- `src/Adapter/Routing/AdminLinkBuilder.php`
- `src/Core/Routing/EntityLinkBuilderFactory.php`

## Related

- [Router Component](../Router/CONTEXT.md) — Symfony router for modern back-office URL generation
- [Twig Component](../Twig/CONTEXT.md) — `LayoutExtension` exposes `getAdminLink()` to templates
