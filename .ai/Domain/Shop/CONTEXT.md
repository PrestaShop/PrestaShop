# Shop Domain

## Purpose

Provides the multi-shop context model — `ShopConstraint`, `ShopId`, `ShopGroupId` — used pervasively across all other domains to scope operations to one shop, a group, or all shops. Also handles shop logo uploads and shop search. It does NOT manage shop creation/deletion (that remains in legacy admin).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Shop/` — Commands, Queries, QueryResults, ValueObjects, DTOs, Exceptions |
| Adapter | `src/Adapter/Shop/` — handler implementations, `Context.php`, `MaintenanceConfiguration`, `ShopUrlDataProvider`, `ShopInformation`, URL providers, Doctrine repositories |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/ShopController.php` |

## Non-obvious patterns

- `ShopConstraint` is the most widely imported value object in the entire codebase — it appears in commands across Customer, Product, Feature, AttributeGroup, and many other domains. Always use it (instead of raw shop IDs) when a command must be shop-scoped.
- `NoShopId` implements `ShopIdInterface` as a null-object sentinel, allowing type-safe "no shop" contexts without nullables.
- `src/Adapter/Shop/Context.php` wraps the legacy `Context::getContext()->shop` for use in DI — it is a compatibility shim, not a CQRS handler.
- The Adapter's `Url/` directory contains multiple URL providers (Category, CMS, Help, ImageFolder, Product, ProductPreview) that are front-end asset helpers, not CQRS concerns.
- Doctrine repositories (`ShopRepository`, `ShopGroupRepository`) live in `src/Adapter/Shop/Repository/`, not in `src/PrestaShopBundle/Entity/Repository/`.

## Canonical examples

- `src/Core/Domain/Shop/ValueObject/ShopConstraint.php` — used throughout all other domain commands
- `src/Core/Domain/Shop/Command/UploadLogosCommand.php` + corresponding adapter handler

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Shop/` — Behat behavior scenarios
