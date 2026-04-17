# AttributeGroup Domain

## Purpose

Manages product attribute groups (e.g., "Color", "Size") and their individual attributes (e.g., "Red", "XL") used to define product combinations. Does not handle product combinations directly — that belongs to the Product domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/AttributeGroup/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Core CQRS (sub-domain) | `src/Core/Domain/AttributeGroup/Attribute/` — full CQRS stack for individual Attributes |
| Adapter | `src/Adapter/AttributeGroup/` — handlers, repository, validator, abstract base handlers |
| Legacy ObjectModel | `classes/AttributeGroup.php` (391 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/AttributeGroupController.php` |

## Non-obvious patterns

- The `Attribute` sub-domain (`AttributeGroup/Attribute/`) has its own complete CQRS stack nested inside this domain — individual attribute CRUD lives here, not in a separate top-level domain.
- The legacy object `Attribute` was renamed to `ProductAttribute` to avoid conflict with the new Attribute system in PHP 8
- `src/Adapter/AttributeGroup/AbstractAttributeGroupHandler.php` and `AbstractAttributeGroupQueryHandler.php` provide shared base classes for handlers — extend these rather than duplicating repository access.
- `src/Adapter/AttributeGroup/AttributeGroupViewDataProvider.php` handles view data assembly outside the standard query handler pattern.

## Canonical examples

- `src/Core/Domain/AttributeGroup/Command/AddAttributeGroupCommand.php` + `src/Adapter/AttributeGroup/CommandHandler/AddAttributeGroupHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/AttributeGroup/` — Behat behavior scenarios
