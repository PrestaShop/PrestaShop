# PrestaShop — Cross-Domain Gotchas

Naming mismatches, identity traps, and non-obvious cross-domain relationships that affect agents working across multiple domains. Sourced from codebase exploration — verified against actual source.

## Naming mismatches

| What you'd expect | What it actually is | Where |
|-------------------|---------------------|-------|
| `Discount` ObjectModel | `CartRule` (2 224 lines) | `classes/CartRule.php` |
| `CatalogPriceRule` ObjectModel | `SpecificPriceRule` | `classes/SpecificPriceRule.php` |
| `CustomerService` domain internals | Everything named `CustomerThread` | `src/Adapter/CustomerService/`, `CustomerThreadRepository` |
| `AliasController` | `SearchAliasController` (under ShopParameters) | `src/PrestaShopBundle/Controller/Admin/` |
| `ContactController` | `ContactsController` (plural) | `src/PrestaShopBundle/Controller/Admin/` |
| `CmsPage` adapter path | `src/Adapter/CMS/Page/` (not `Adapter/CmsPage/`) | — |
| `CmsPageCategory` adapter path | `src/Adapter/CMS/PageCategory/` | — |

## Identity traps

**Address — duplication on order edit**
- When an address linked to an order is modified, PrestaShop duplicates it and creates a new row with a new `AddressId`
- The original address data is preserved for the existing order

**Carrier — dual ID**
- `CarrierId` changes on every edit when it is assigned to an order (PrestaShop copies the row and increments the ID on update)
- `CarrierReferenceId` is stable across edits — use this for any persistent reference to a carrier
- Never store `CarrierId` long-term; always resolve via `CarrierReferenceId`

**Module — string identity**
- Primary identity is `ModuleTechnicalName` (the folder name string), not `ModuleId` (int)
- CQRS commands take `ModuleTechnicalName`; the int ID is legacy-only

**Tab — class name identity**
- `Tab` is identified by its controller class name string (e.g. `AdminProductsController`), not a numeric ID
- No Query layer — reads go through `TabDataProvider` directly

## Cross-domain write ownership

| Entity | Who owns writes |
|--------|----------------|
| CartRule (discount codes) | **Discount** domain — not CartRule (CartRule domain is query-only) |
| Credit slips | **Order** domain — CreditSlip domain is query-only |
| Combination | Sub-entity of **Product** — `CombinationId` is always scoped to a `ProductId` |
| Order invoice / payment / product line | Sub-domains inside **Order** (`Order/Invoice/`, `Order/Payment/`, `Order/Product/`) |
| Customer group membership | **Customer** domain — `Group` is a sub-domain inside Customer |

## Adapter path exceptions

Most domains follow `src/Adapter/{DomainName}/`. Exceptions:
- `CmsPage` → `src/Adapter/CMS/Page/`
- `CmsPageCategory` → `src/Adapter/CMS/PageCategory/`
- `ImageSettings` → handlers live in `src/Core/Domain/ImageSettings/CommandHandler/` (no separate Adapter folder)
- `Theme` → handlers live in `src/Core/Domain/Theme/CommandHandler/` (Core, not Adapter — unusual)

## Handlers in Core (not Adapter)

Normally: interfaces in Core, concrete implementations in Adapter. Exceptions where concrete handlers are in Core:
- `ImageSettings` domain handlers
- `Theme` domain handlers

When adding a handler to these domains, follow the existing pattern (Core) rather than the standard pattern. When handlers are migrated to the future persistence system (probably Doctrine), they should be moved into the Core namespace.

## Legacy class size warnings

| Domain | Legacy class | Lines | Risk |
|--------|-------------|-------|------|
| Discount | `classes/CartRule.php` | ~2 224 | God object — never add logic |
| Cart | `classes/Cart.php` | ~5 000 | God object — never add logic |
| Product | `classes/Product.php` | ~6 000+ | God object — never add logic |
| Order | `classes/Order.php` | ~2 000+ | God object — never add logic |
| Customer | `classes/Customer.php` | ~1 500+ | Do not extend |
