# Locale Component

## Purpose

Locale-aware number and price formatting using Unicode CLDR data: `Locale` and `Currency` value objects with formatting specifications, a layered repository with caching, and adapter classes for persisting localization settings. Does not handle translation strings — those are Symfony's Translation component.

## Layers

| Layer | Path |
|-------|------|
| Domain objects (`Locale`, `Currency`) | `src/Core/Localization/` |
| Formatting specifications (CLDR) | `src/Core/Localization/Specification/` |
| Locale + Currency repositories | `src/Core/Localization/Locale/`, `src/Core/Localization/Currency/` |
| CLDR data layer + caching | `src/Core/Localization/CLDR/` |
| Adapter (settings + legacy bridge) | `src/Adapter/Localization/` |

## Non-obvious patterns

- `LanguageContext` (Context component) wraps `LocaleInterface` — use it for formatting in back-office code; don't inject `LocaleRepository` directly in controllers
- Currency symbols and names are **per-locale** — `Currency::getSymbol(localeCode)` / `getName(localeCode)` vary by locale; there is no single "currency symbol"
- CLDR data is read from JSON files at runtime with a cache layer — `Reader` + `LocaleCache` + `CurrencyCache` form a three-layer stack

## Canonical examples

- `src/Core/Localization/Locale.php` + `src/Core/Localization/Locale/Repository.php`
- `src/Core/Localization/Specification/Price.php`
- `src/PrestaShopBundle/Twig/Extension/LocalizationExtension.php`

## Related

- [Context Component](../Context/CONTEXT.md) — `LanguageContext` and `CurrencyContext` wrap locale/currency objects
- [Configuration Component](../Configuration/CONTEXT.md) — `LocalizationConfiguration` persists locale settings
