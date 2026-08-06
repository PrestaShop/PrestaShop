# Carrier Domain

## Purpose

Manages shipping carriers, their shipping ranges (by price and weight), and zone assignments. Does not handle order shipping state or tracking — those belong to the Order domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Carrier/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Carrier/` — handlers, repositories (CarrierRepository, CarrierRangeRepository), validator |
| Legacy ObjectModel | `classes/Carrier.php` (1709 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/Shipping/CarrierController.php` |

## Non-obvious patterns

- Carriers have **two distinct identifiers**: `CarrierId` (the row primary key, changes on edit when assigned to an order because PrestaShop duplicates the carrier) and `CarrierReferenceId` (stable reference id that persists across edits). Always use `CarrierReferenceId` when linking carriers to other entities across versions.
- Shipping ranges are first-class ValueObjects: `CarrierRangePrice`, `CarrierRangeZone`, `CarrierRangesCollection` — range management is handled via a dedicated `CarrierRangeRepository`.
- `CarrierConstraints.php` (plural) holds all constraint constants — note the plural naming differs from the singular pattern used in most other domains.

## Canonical examples

- `src/Core/Domain/Carrier/Command/AddCarrierCommand.php` + `src/Adapter/Carrier/CommandHandler/AddCarrierCommandHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Carrier/` — Behat behavior scenarios
- [GOTCHAS.md](../../GOTCHAS.md) — CarrierId vs CarrierReferenceId dual identity trap
