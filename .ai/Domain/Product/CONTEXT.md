# Product Domain

## Purpose

Manages the full product catalog lifecycle: creation, updates, duplication, combinations, images, pricing, stock, suppliers, categories, SEO, customization fields, packs, virtual files, and multi-shop behavior. Does not handle cart or order creation.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Product/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Sub-domains | `src/Core/Domain/Product/{Combination,SpecificPrice,Stock,Image,Supplier,Pack,VirtualProductFile,Customization,FeatureValue,Attachment,Shop,AttributeGroup}/` — each has its own full CQRS structure |
| Adapter | `src/Adapter/Product/` — handler implementations, repositories, updaters, fillers, validators |
| Form layer | `src/Core/Form/IdentifiableObject/` — `ProductFormDataProvider`, `ProductFormDataHandler`, `ProductCommandsBuilder` + 14 specialized builders |
| Legacy ObjectModel | `classes/Product.php` (8 000+ lines) + companion ObjectModels — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/Product/`, grid factories, 97-file Vue/TS frontend in `admin-dev/themes/new-theme/js/pages/product/` |

## Non-obvious patterns

- **12 sub-domains** each have their own full CQRS layer and adapter layer under `src/Core/Domain/Product/{Name}/` and `src/Adapter/Product/{Name}/` — treat each as a mini-domain
- **Filler pattern**: `UpdateProductHandler` delegates to `ProductFiller` which splits the command across `BasicInformationFiller`, `PricesFiller`, `SeoFiller`, etc. — one filler per form section
- **Command builders**: the product form is too complex for a single `FormDataHandler`; `ProductCommandsBuilder` delegates to 14 specialized builders that each produce a typed CQRS command from raw form data
- `GetProductForEditing` returns `ProductForEditing`, a large aggregate DTO — the single source of truth for populating the entire edit form

## Canonical examples

- `src/Core/Domain/Product/Command/UpdateProductCommand.php` + `src/Adapter/Product/CommandHandler/UpdateProductHandler.php`
- `src/Core/Domain/Product/Query/GetProductForEditing.php` + `src/Core/Domain/Product/QueryResult/ProductForEditing.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Cart Domain](../Cart/CONTEXT.md) — references `ProductId` via `AddProductToCartCommand`
- [Forms Component](../../Component/Forms/CONTEXT.md) — `ProductFormDataHandler`, `ProductCommandsBuilder`
- [Grid Component](../../Component/Grid/CONTEXT.md) — `ProductGridDefinitionFactory`
- [DevDocs](https://devdocs.prestashop-project.org/9/development/page-reference/back-office/catalog/products/)
- `tests/Integration/Behaviour/Features/Scenario/Product/` — Behat behavior scenarios
