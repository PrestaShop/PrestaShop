# Context Component

## Purpose

Immutable, injectable representations of the current execution environment: active shop, language, currency, country, and authenticated employee. Replaces the legacy `Context::getContext()` singleton. Does not perform authentication or session management.

## Layers

| Layer | Path |
|-------|------|
| Value objects | `src/Core/Context/` — `ShopContext`, `LanguageContext`, `CurrencyContext`, `CountryContext`, `EmployeeContext`, `LegacyControllerContext`, `ApiClientContext` |
| Builders | `src/Core/Context/*Builder.php` — one builder per context object, all implement `LegacyContextBuilderInterface` |
| Subscribers (BO) | `src/PrestaShopBundle/EventListener/Admin/Context/` — populate contexts from HTTP request on each Symfony event |
| Subscribers (Admin API) | `src/PrestaShopBundle/EventListener/API/Context/` — populate contexts for API requests |
| Subscribers (Console) | `src/PrestaShopBundle/EventListener/Console/Context/` — populate contexts for CLI commands |

## Non-obvious patterns

- All context objects are `readonly` — built once per request by subscribers, never mutated
- Each builder has `buildLegacyContext()` to sync the old `Context::getContext()` global — necessary during the migration period
- `LanguageContext` implements both `LanguageInterface` **and** `LocaleInterface` — it can format numbers and prices directly
- Each listener set depends on which Application/Kernel is running — configurations are in `app/config/admin/`, `app/config/admin-api/`, etc.
- **In new code, inject the context service** (`LanguageContext`, `ShopContext`, `CurrencyContext`, `EmployeeContext`) — never read values from the legacy `Context::getContext()` global, and never pass legacy-context values into services, query builders, or repositories. For example, a grid query builder needing the current language must depend on `LanguageContext`, not on the injected legacy context. `buildLegacyContext()` exists only to sync the old global during migration — it is not an entry point for reading

## Canonical examples

- `src/Core/Context/ShopContext.php`
- `src/Core/Context/ShopContextBuilder.php`

## Related

- [Configuration Component](../Configuration/CONTEXT.md) — `ShopContext::getShopConstraint()` scopes config writes
- [Locale Component](../Locale/CONTEXT.md) — `LanguageContext` wraps `LocaleInterface` for formatting
