# Address Domain

## Purpose

Manages customer and order postal addresses (creation, editing, deletion, validation). Does not handle address formatting for display — that belongs to the Country/geolocation layer.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Address/` — Commands, Queries, QueryResults, ValueObjects, Exceptions, Configuration |
| Adapter | `src/Adapter/Address/` — handler implementations, repositories |
| Legacy ObjectModel | `classes/Address.php` (654 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Address/AddressController.php` |

## Non-obvious patterns

- `Configuration/AddressConstraint.php` holds validation constraint constants used across handlers — check here before adding new validation logic.
- `AddressId` is referenced directly from the Order domain (delivery/invoice address commands) and from the Customer domain query results — changes to this ValueObject have cross-domain impact.
- When an address is modified, if it was used by an order the address is duplicated to keep the old data unchanged. A new Address is created with a new ID that will be used for future usages.

## Canonical examples

- `src/Core/Domain/Address/Command/AddAddressCommand.php` + `src/Adapter/Address/CommandHandler/AddAddressCommandHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Address/` — Behat behavior scenarios
