# BackOfficeHelp Component

## Purpose

Generates contextual documentation links for the back-office sidebar help panel pointing to the PrestaShop DevDocs for the current page and language. Does not provide inline tooltips or help text.

## Layers

| Layer | Path |
|-------|------|
| Core | `src/Core/Help/` — `Documentation` class builds versioned doc URLs |
| Adapter | `src/Adapter/Shop/Url/HelpProvider.php` — wraps `Documentation` with runtime context |
| Twig | `src/PrestaShopBundle/Twig/Extension/DocumentationLinkExtension.php` — exposes links to templates |

## Non-obvious patterns

- `Documentation` is a plain concrete class with no interface — not the usual Core pattern
- `HelpProvider` implements `UrlProviderInterface`, not a help-specific contract

## Canonical examples

- `src/Core/Help/Documentation.php`

## Related

- [Router Component](../Router/CONTEXT.md) — `HelpProvider` uses the router for sidebar URLs
- [Context Component](../Context/CONTEXT.md) — language ISO code comes from `LanguageContext`
