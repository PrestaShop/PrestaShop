# Tag Domain

## Purpose

Manages keyword tags associated with products, used to improve search relevance on the front office. Does not own the product-tag relationship itself; association is managed through the Product domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Tag/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Tag/` — CommandHandler, QueryHandler |
| Legacy ObjectModel | `classes/Tag.php` (362 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShopParameters/TagController.php`, `src/Core/Grid/Definition/Factory/TagGridDefinitionFactory.php`, `src/PrestaShopBundle/Form/Admin/Configure/ShopParameters/Tag/TagType.php` |

## Non-obvious patterns

- Tags are also embedded inside the Product form via `src/PrestaShopBundle/Form/Admin/Type/TaggedItemCollectionType.php` and `TaggedItemType.php` — these are generic reusable form types, not Tag-domain-specific components.

## Canonical examples

- `src/Core/Domain/Tag/Command/AddTagCommand.php` + `src/Adapter/Tag/CommandHandler/`
- `src/Core/Domain/Tag/Query/GetTagForEditing.php` + `src/Adapter/Tag/QueryHandler/`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Tag/` — Behat behavior scenarios
