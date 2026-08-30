# OrderState Domain

## Purpose

Manages the configurable set of order statuses (e.g., "Payment accepted", "Shipped") including their labels, colors, associated email templates, and icon images. Does not handle order state transitions on individual orders (that is the Order domain's responsibility).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/OrderState/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/OrderState/` — handler implementations, `AbstractOrderStateHandler.php`, `OrderStateDataProvider.php` (no repository — handlers use the legacy ObjectModel directly) |
| Legacy ObjectModel | `classes/order/OrderState.php` (171 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShopParameters/OrderStateController.php`, `src/Core/Grid/Definition/Factory/OrderStatesGridDefinitionFactory.php` |

## Non-obvious patterns

- `OrderStateFileUploaderInterface` in the domain root is a domain-level interface for uploading the state icon image; its implementation lives outside the Adapter layer and is injected via DI — icon upload is part of Add/Edit, not a separate command.
- `OrderStateSettings` at the domain root holds field-length constants; check it before adding new constraints.
- There is no `Repository` class in `src/Adapter/OrderState/` — handlers load and persist via the legacy `OrderState` ObjectModel directly.
- Order state is listed under **Shop Parameters** in admin navigation, not under Orders or Catalog.

## Canonical examples

- `src/Core/Domain/OrderState/Command/AddOrderStateCommand.php` + `src/Adapter/OrderState/CommandHandler/AddOrderStateHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/OrderState/` — Behat behavior scenarios
