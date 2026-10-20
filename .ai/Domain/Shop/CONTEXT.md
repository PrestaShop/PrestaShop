# Shop Domain

## Purpose

Provides the multi-shop context model — `ShopConstraint`, `ShopId`, `ShopGroupId` — used pervasively across all other domains to scope operations to one shop, a group, or all shops. Also handles shop logo uploads, shop search, and shop, shop group and shop URL CRUD.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Shop/` — Commands, Queries, QueryResults, ValueObjects, DTOs, Exceptions |
| Adapter | `src/Adapter/Shop/` — handler implementations, `Context.php`, `MaintenanceConfiguration`, `ShopUrlDataProvider`, `ShopInformation`, URL providers, Doctrine repositories |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/ShopController.php`, `ShopGroupController.php` and `ShopUrlController.php` (behind the `multishop` feature flag; the shop routes use `_legacy_controller: AdminShopGroup`, there is no `AdminShop` tab) |

## Non-obvious patterns

- `ShopConstraint` is the most widely imported value object in the entire codebase — it appears in commands across Customer, Product, Feature, AttributeGroup, and many other domains. Always use it (instead of raw shop IDs) when a command must be shop-scoped.
- `NoShopId` implements `ShopIdInterface` as a null-object sentinel, allowing type-safe "no shop" contexts without nullables.
- `src/Adapter/Shop/Context.php` wraps the legacy `Context::getContext()->shop` for use in DI — it is a compatibility shim, not a CQRS handler.
- The Adapter's `Url/` directory contains multiple URL providers (Category, CMS, Help, ImageFolder, Product, ProductPreview) that are front-end asset helpers, not CQRS concerns.
- Doctrine repositories (`ShopRepository`, `ShopGroupRepository`, `ShopUrlRepository`) live in `src/Adapter/Shop/Repository/`, not in `src/PrestaShopBundle/Entity/Repository/`.
- Shop group sharing options (`share_customer`, `share_stock`, `share_order`) are locked once the store has more than one active shop, and `share_order` requires the other two. Toggling `share_stock` resets the group's `stock_available` quantities (legacy `StockAvailable::resetProductFromStockAvailableByShopGroup()`).
- Each shop keeps exactly one main URL, always enabled: the first URL added to a shop becomes main, setting another URL as main demotes the previous one (legacy `ShopUrl::setMain()`), and a main URL can be neither disabled, unset, moved to another shop nor deleted; setting a disabled URL as main from the grid enables it. Adding, editing or deleting a shop URL regenerates `.htaccess` and `robots.txt` and clears the Smarty and media caches (`ShopUrlChangeHandler`).
- `Shop::add()` alone does not make a usable shop: `AddShopHandler` also copies data from a source shop (legacy `Shop::copyShopData()`, which always copies currencies and `category_lang` and fires `actionShopDataDuplication`), adds the default customer groups and the CMS root category when they are not imported, associates the super admins, the categories (`Category::updateFromShop()`) and sets `PS_HOME_CATEGORY` for the shop (`ShopCategoriesUpdater`). A new shop has no URL until one is added on the shop URLs page.
- A shop of a group sharing customers, orders or stock cannot change group, and cannot move to such a group when it has customers or orders. The default shop and a shop with customers or orders cannot be deleted.

## Canonical examples

- `src/Core/Domain/Shop/ValueObject/ShopConstraint.php` — used throughout all other domain commands
- `src/Core/Domain/Shop/Command/UploadLogosCommand.php` + corresponding adapter handler

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Shop/` — Behat behavior scenarios
