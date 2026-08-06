# Shipment Domain

## Purpose

Manages shipments within an order — creating, editing, splitting, merging, and switching carriers for shipments and their product quantities. It does NOT manage order-level payment or delivery address; those remain in the Order domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Shipment/` — Commands, Queries, QueryResults, ValueObjects, Exceptions, Service interfaces |
| Adapter | `src/Adapter/Shipment/` — handler implementations, delivery options, `OrderShipmentCreator`, `ShipmentMerger`, `ShipmentSplitter`, `ShipmentTotalsCalculator` |
| Doctrine Entity | `src/PrestaShopBundle/Entity/Shipment.php`, `ShipmentProduct.php`, `Entity/Repository/ShipmentRepository.php` |

## Non-obvious patterns

- This domain uses Doctrine entities (no legacy ObjectModel) — `Shipment` and `ShipmentProduct` are in `src/PrestaShopBundle/Entity/`.
- Service interfaces (`ShipmentMergerInterface`, `ShipmentSplitterInterface`) are declared in `Core/Domain/Shipment/Service/` rather than in the Adapter, making the core self-contained for testing.
- `OrderDetailId` / `OrderDetailsId` / `OrderDetailQuantity` are domain value objects here rather than borrowing from the Order domain, reflecting that shipments operate at the order-line level.
- Shipment is partially embedded in the Order viewing result (`OrderProductForViewing` references shipment data) but has its own dedicated query stack.

## Canonical examples

- `src/Core/Domain/Shipment/Command/CreateShipment.php` + `src/Adapter/Shipment/CommandHandler/` (handler)
- `src/Core/Domain/Shipment/Query/GetShipmentForEditing.php` + `src/Core/Domain/Shipment/QueryResult/ShipmentForEditing.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Shipment/` — Behat behavior scenarios
